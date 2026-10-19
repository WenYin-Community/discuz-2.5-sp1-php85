# AGENTS.md

## 项目概览

- 2005 年的 Discuz! 2.5F SP1 简体中文版（GBK）论坛程序，PHP 4 时代代码库，已被上游停止维护。
- 没有 Composer/npm、构建、Lint、自动化测试；当前目录也不是 Git 仓库。
- 可部署源码全部在 `upload/`；仓库根目录的 `*.txt` 是 GBK 编码的官方文档。
- 本包带有 `//fix: BY pk0909` 本地补丁（`index.php`、`forumdisplay.php`、`include/forum.php`、`admin/global.php`、`utilities/upgrade25.php` 等），不要回退这些修复。

## 目录结构

- `upload/*.php`：前台入口，一文件一功能（`index.php`、`forumdisplay.php`、`viewthread.php`、`post.php`、`memcp.php`、`admincp.php` 等）。
- `upload/include/`：公共库。`common.php` 是引导核心，`global.php` 是函数库，`db_mysql.php` 是数据库类，`template.php` 是模板编译器，`cache.php` 是缓存生成器；`forum.php`、`post.php`、`misc.php` 等为多个入口共享的业务逻辑。
- `upload/admin/`：后台业务脚本；`upload/admin/tpl/`：后台视图与语言包。
- `upload/templates/default/`：默认模板（`*.htm`）与语言包（`*.lang.php`）。
- `upload/forumdata/`：运行时目录。`cache/` 存生成的缓存 PHP，`templates/` 存编译后的模板；两者都由程序生成，不要手改。
- `upload/plugins/`：插件 PHP 与 `plugin.lang.php`。
- `upload/attachments/`、`upload/customavatars/`：用户上传目录，必须可写且可被 Web 访问。
- `utilities/`：独立升级/修复/转换脚本，不属于部署内容；必须复制到论坛根目录才能运行，用完删除。

## 运行与验证

- 运行时要求：PHP ≤ 5.6（代码使用 `mysql_*`、`set_magic_quotes_runtime`、模板编译器中的 `preg_replace /e`，PHP 7+ 无法运行）；MySQL 5.1 或更早（`install.php` 建表 SQL 含 `TYPE=HEAP` 等旧语法，MySQL 8 会拒绝）。当前开发机未安装 PHP，MySQL 服务未运行，无法本地启动或做 HTTP 冒烟测试。
- 语法检查：`php -l upload/include/common.php`（仅检查语法，不验证运行）。
- 本地临时启动（需 PHP 5.4–5.6）：`php -S 127.0.0.1:8080 -t upload`。
- 部署：把 `upload/` 内容放到 Web 根目录；`config.php` 填数据库账号；将 `templates/`、`attachments/`、`customavatars/`、`forumdata/`、`forumdata/cache/`、`forumdata/templates/`、`forumdata/accesslogs/` 设为 777；浏览器访问 `install.php` 完成安装。`forumdata/accesslogs/` 初始不存在，需手动创建。
- 安装后必须删除 `install.php`，不要把 `utilities/` 上传到服务器；这些脚本可修改数据库或重置管理员。
- 修改 `templates.lang.php` 后必须到后台执行“更新缓存”，否则编译模板仍使用旧文案。
- 无自动测试。改动后的最低验证：`php -l` + 浏览器手工走首页、版块、主题、发帖、短消息、后台设置。

## 请求与分层

- 前台入口模式：定义 `CURRSCRIPT` → `require './include/common.php'` → 按需 `require DISCUZ_ROOT.'./include/xxx.php'` → `include template('xxx')` → 结束时 `output()` 或 `dexit()`。
- `include/common.php` 负责：加载 `config.php`、`global.php`、数据库类；模拟 `register_globals`（对 `$_GET`/`$_POST` 做 `daddslashes` 后 `extract`）；恢复会话、用户组、风格；缓存缺失时自动调用 `updatecache()` 并输出“刷新页面”提示。
- `include/` 下的文件必须保留 `if(!defined('IN_DISCUZ')) exit('Access Denied');` 守卫；后台脚本使用 `IN_ADMINCP`。
- 后台路由：`admincp.php` 按 `action` 白名单映射到 `admin/*.php`；视图在 `admin/tpl/*.php`，页面统一用 `cpheader()` / `cpfooter()` / `cpmsg()`，表单用 `showsetting()`，分组用 `showtype()`。后台文案在 `templates/default/admincp.lang.php`。
- 插件：`plugins.php?p=<key>` 加载 `plugins/<key>.php`；元数据与配置存 `cdb_plugins`、`cdb_plugins_settings`；插件文案放 `plugins/plugin.lang.php` 的 `$plglang` / `$msglang`。

## 数据库

- 通过 `include/db_mysql.php` 的 `dbstuff` 类访问，底层是已移除的 `mysql_*` 扩展；不引入 PDO/MySQLi，除非明确要求整体迁移。
- 表名通过全局变量 `$table_<name>` 使用，前缀由 `config.php` 的 `$tablepre` 决定（默认 `cdb_`）。
- 查询是原生 SQL 字符串：`$db->query()`、`$db->fetch_array()`、`$db->result()`、`$db->num_rows()`、`$db->insert_id()`、`$db->affected_rows()`；可用类型标记 `CACHE`、`UNBUFFERED`、`SILENT`。
- 完整表结构（37 张 `cdb_*` 表）在 `upload/install.php` 的 heredoc SQL 中；升级逻辑在 `utilities/upgrade*.php`。
- 拼接 SQL 前对整数用 `intval()`、对字符串用 `addslashes()`；这是本项目的既有安全边界，不要移除。

## 编码与代码风格

- 所有原始文件是 GB2312/GBK 编码 + CRLF 换行，`file` 会显示 ISO-8859；页面声明 `charset=gb2312`。编辑中文时必须保持 GBK 与 CRLF，不要转成 UTF-8。
- 查看乱码文件：`iconv -f gb2312 -t utf-8 <file>`；写回时反向转换。改动前后用 `file` 确认编码未被破坏。
- 代码是 PHP 4 风格：`var` 声明、无命名空间/类型声明/严格模式、短标签 `<?`、大量全局变量。修改现有文件时匹配该风格，不要顺手现代化。
- 用户输入经 `extract` 后以裸变量出现（如 `$action`、`$fid`、`$tid`），先校验再使用；输出 HTML 前用 `dhtmlspecialchars()`。
- 会修改数据的 POST 表单必须带隐藏 `formhash` 并调用 `submitcheck('submitname')`；前台错误/跳转用 `showmessage()`，后台用 `cpmsg()`。
- 权限判断依赖 `$adminid`、`$groupid`、`$isadmin`、`$forum[...]` 权限位、`modcheck()`；不要弱化这些检查。
- 链接统一经 `transsid()` 或 `output()` 处理（Cookie 被禁用时自动附加 `sid`）。

## 模板、语言与缓存

- 模板语法：`{lang key}`、`{template name}`、`{if expr}...{elseif}...{else}...{/if}`、`{loop $arr $v}...{/loop}`、`$var`；`<!--{...}-->` 在编译时被剥离。
- 模板在首次访问时编译到 `forumdata/templates/{templateid}_{name}.tpl.php`；源 `.htm` 更新且 `tplrefresh=1` 时自动重编译，编译产物不要手改。
- 缓存由 `include/cache.php` 生成到 `forumdata/cache/`（`cache_*.php`、`style_*.php`、`usergroup_*.php`），删除后会自动重建，但会先输出“Caches successfully created, please refresh.”。
- 官方文档明确要求不要修改 `templates/default/`；需要换肤时新建 `templates/<name>/`，只放有差异的 `.htm`/`.lang.php`，再在后台注册模板套系与风格。
- CSS 与配色来自 `css.htm` 及数据库中的 `stylevars`；尺寸/颜色常量如 `{IMGDIR}`、`{MAINTABLEWIDTH}` 由风格缓存注入。

## 安全与已知风险

- 这是 EOL 代码，历史上存在大量 SQL 注入、XSS、任意文件包含/删除类漏洞。不要以“现代化”为由重写认证、权限或数据库层；改动敏感逻辑前先说明影响面。
- 永远不要放宽 `IN_DISCUZ` / `IN_ADMINCP` 守卫，也不要在生产环境保留 `install.php`、`utilities/` 或可写的 `forumdata/` 直链访问。
- `config.php` 含数据库凭据；本仓库中是占位值。不要把真实密码写入仓库或提交到版本库。
- 生产环境将 `$errorreport` 设为 0、`$debug_on` 设为 0，避免泄露 SQL 与路径。

## 改动前先读

- `template.txt`：模板/风格/多语言机制。
- `plugin.txt`：2.5 插件规范与后台插件中心。
- `language.txt`：语言包与 `templates.lang.php` 的缓存要求。
- `files.txt`：目录职责与 777 权限清单。
- `upgrade.txt`、`changelog.txt`：版本差异与 SP1 修复内容。
- 涉及权限、认证、安装、缓存时，先读 `include/common.php`、`include/global.php`、`include/cache.php`、`admin/global.php`、`install.php`。
- 以上文档均为 GBK，阅读前用 `iconv -f gb2312 -t utf-8` 转码。
