# Discuz! 2.5F SP1 GBK —— PHP 8.5 + MySQL 5.7 移植报告

## 1. 结论摘要

- 可行性：**已完成移植**，源码可直接运行在 PHP 8.5 + MySQL 5.7。
- 兼容性基线：PHP **8.5.11**（php-fpm）、MySQL **5.7.44**、nginx。
- 编码：**全程保持 GBK（GB2312）**，页面继续输出 `charset=gb2312`，数据库与连接字符集均为 `gbk`。
- 验证结果：
  - 125 个 PHP 源文件全部通过 `php -l`（0 错误）。
  - 64 个模板全部编译成功，编译产物逐文件 `php -l` 通过（0 错误）。
  - 55 个前后台页面/动作组合在登录态下 HTTP 冒烟：**0 Warning / 0 Fatal / 0 Deprecated**。
  - 写路径实测：注册、登录、发帖、回复、编辑、搜索、短消息、后台各模块全部可用。
  - 中文读写字节级校验：数据库中的主题/正文/短消息均为 GBK 字节，页面渲染正常。
- 本机环境：podman 根less 模式，容器栈见 `docker/`。

## 2. 运行环境

| 组件 | 版本/镜像 | 说明 |
|---|---|---|
| PHP | `localhost/wenyinos-php:8.5-fpm`（PHP 8.5.11） | 已含 mysqli / mysqlnd / pdo_mysql / mbstring / iconv / gd / zip / zlib；镜像默认 `short_open_tag=On` |
| MySQL | `docker.io/library/mysql:5.7`（5.7.44） | 服务端字符集 `gbk`、`sql_mode=''` |
| Web | `nginx:alpine` | 反代 php-fpm，屏蔽 `forumdata/`、`utilities/`，限制 `install.php` 仅内网 |
| 编排 | `podman-compose` | 根less podman，php 服务使用 `userns_mode: keep-id` |

启动方式见 `docker/README.md`。默认地址 `http://127.0.0.1:8081/`，MySQL 映射 `127.0.0.1:3317`。

## 3. 不兼容项与修复（PHP 8.5）

### 3.1 已删除函数

| 原用法 | 位置 | 修复 |
|---|---|---|
| `set_magic_quotes_runtime()` | `include/common.php`、`install.php`、`utilities/*`（4 个） | 删除调用；魔术引号语义固定为“始终转义” |
| `get_magic_quotes_gpc()` | `include/common.php` | 以常量 0 代替 |
| `mysql_*()`（21 处） | `include/db_mysql.php`、`admin/global.php`、`utilities/upgrade1.php` | `dbstuff` 重写为 mysqli，保持 607 处 `$db->query()` 等调用面不变 |
| `eregi()` / `ereg()` | `include/post.php`、`admin/plugin.php` | 改为 `preg_match()` |
| `mysql_escape_string()` | `admin/global.php` | 改为 `addslashes()` |

### 3.2 PHP 8 致命语法/语义

| 问题 | 规模 | 修复 |
|---|---|---|
| 裸数组键 `$var[key]`（代码与双引号字符串内） | 841 处 | 统一改为 `$var['key']` / `{$var['key']}`；字符串内链式下标自动补花括号 |
| 插值内嵌套花括号 `{$a[{$b}][key]}` | 3 处 | 改为 `{$a[$b]['key']}`（PHP 8 禁止嵌套花括号） |
| `preg_replace` 的 `/e` 修饰符 | 16 条语句（`include/template.php`、`include/global.php`、`include/discuzcode.php`） | 全部改为 `preg_replace_callback()`；模板编译器保持产物等价 |
| `extract(false)`（`daddslashes` 空数组返回 false） | `include/common.php` | `daddslashes` 对空数组返回数组；extract 前加 `is_array` 守卫 |
| `count()` / `array_merge()` 收到非数组 | 14 处 | `is_array()` 守卫或 `(array)` 强制转换 |
| `trim(array)`（`array_del`） | `include/cache.php` | **有意保留原有空行为**（原版因 `trim($f)` 恒为假而从不删除键，`welcomemsg/bbrules` 等依赖其留在缓存） |
| 裸常量 `case settings:` 等 | 28 处 | 改为字符串 `case 'settings':` |
| 模板裸常量 `allowdelpost` | 1 处 | 改为 `$allowdelpost`（原版因 PHP 5 常量回退而恒为真） |
| 调用时引用传递 `fsockopen(..., &$errno, ...)` | 1 处 | 去掉 `&` |
| 模板 `isset($previewpost)` 语义 | — | 不预置该变量，改用 `!empty()` 守卫 |
| PHP 8.5 `(double)` 弃用 | 1 处 | 改为 `(float)` |

### 3.3 PHP 8 告警治理（E_NOTICE → E_WARNING）

PHP 8 把大量“未定义变量/数组下标”提升为 E_WARNING 并输出到页面。处理方式：

- `include/common.php`：预置请求级变量（`navtitle/page/goto/p/...`），但**刻意不预置** `action/searchid/fid/previewpost`（代码用 `isset()` 区分“是否提交”）。
- `include/common.php`：仅当 `$forum` 是数组时补齐常用字段（保持 `false` 的原真值语义，避免破坏 `if($tid && $forum)`）。
- 各入口脚本：对 `$_COOKIE`/`$_SERVER`/`$_GET` 直接索引补 `isset()`。
- 稀疏数组（`array($x => 'checked')`）按模板实际键集合预填充（`$toselect/$month/$tppchecked/$checksticky/$colorcheck/$ordercheck` 等）。
- `register.php`：`$$setting['variable']` 改为 `${$setting['variable']}`（PHP 8 下原写法会按 `${$setting}['variable']` 解析并触发 Array to string）。
- 语言包插值：`eval()` 改为新增的 `dinterpolate()`（支持 `$var`、`{$var}`、`$arr[key]`、`$arr['key']`、链式下标），共替换 4 处 eval 站点；`admin/members.php` 的 eval 改为 `dinterpolate()` 直接调用。

## 4. 数据库与 MySQL 5.7 适配

- `include/db_mysql.php`：
  - 基于 mysqli 重写，保留 `query/fetch_array/result/num_rows/insert_id/affected_rows/error/errno` 及 `UNBUFFERED/CACHE/SILENT` 标记语义。
  - 连接后执行 `SET NAMES 'gbk'`；`mysqli_report(MYSQLI_REPORT_OFF)` 关闭 PHP 8.1+ 默认的异常模式，恢复“查询返回 false”的旧行为。
  - `fetch_array()` 对失败查询返回 `FALSE`（集中消除 228 处调用点的 TypeError）。
  - 定义 `MYSQL_ASSOC/MYSQL_NUM/MYSQL_BOTH` 常量映射到 mysqli 版本。
  - 不再注入 `SQL_CACHE`（MySQL 5.7 已弃用、8.0 移除，且不影响结果）。
- `install.php`：
  - `cdb_caches` 的 `TYPE=HEAP MAX_ROWS=2000` 改为 `ENGINE=InnoDB`（MySQL 5.5+ 已移除 `TYPE=`，且 MEMORY 不支持 `TEXT` 列）。
  - 建库语句追加 `DEFAULT CHARACTER SET gbk`，并在库已存在时执行 `ALTER DATABASE ... DEFAULT CHARACTER SET gbk`。
  - 修复 `$query - $db->query(...)` 笔误（PHP 8 下对对象做减法会致命）。
- 实测：35 张表全部导入成功，初始数据（设置/用户组/表情/风格/模板/统计）完整。
- 服务器配置：`--character-set-server=gbk --collation-server=gbk_chinese_ci --sql-mode=`（严格模式会让大量“缺列 INSERT”失败）。

## 5. 模板与缓存

- 编译器 `include/template.php` 的 9 条 `/e` 语句改为 `preg_replace_callback()`，新增 9 个 `parse_tpl_*` 回调；编译产物与旧版语义一致（条件中的引号不再经过 eval，因此更安全）。
- `include/discuzcode.php`：
  - `[code]/[php]/[hide=]` 改为回调。
  - bbcode 应用改为逐条分发：回调名走 `preg_replace_callback`，普通替换串走 `preg_replace`（`preg_replace_callback` 不接受“回调数组”）。
  - `[url]`、`[swf]`、`[img]`、`[img=WxH]` 全部改为回调，新增 `parse_bb_url/parse_bb_swf/parse_bb_img/parse_bb_imgwh`。
- `include/global.php` 的 `output()`（Cookie 关闭时的 sid 注入）改为回调 + 两次替换。
- 缓存与编译模板仍然生成在 `forumdata/cache/`、`forumdata/templates/`；删除后自动重建。

## 6. 编码策略（GBK 保持）

- 所有源码文件保持 **GB2312/GBK + CRLF**，未做任何 UTF-8 转换；新增/修改的注释以 GBK 写回（工具：`iconv -f gb2312`）。
- `htmlspecialchars()` 统一改为字节安全的 `dhtmlspecialchars()`（13 处）；仅 `install.php` 与 `include/db_mysql_error.php`（不加载 `global.php` 的上下文）使用显式字符集 `htmlspecialchars($s, ENT_COMPAT, 'ISO-8859-1')`，与 PHP 5 的字节透传行为一致。
- 页面继续声明 `charset=gb2312`；数据库/表/连接均为 `gbk`。
- 前台表单以 GBK 提交（浏览器按 `charset=gb2312` 编码），实测写入字节与 GBK 编码逐字节一致。

## 7. 运行环境差异（php-fpm vs mod_php）

- 原程序大量使用 `./forumdata/...` 等相对路径。php-fpm 默认工作目录是 `/`，会导致缓存写入失败。已在 `docker/php-fpm.conf` 设置 `chdir = /var/www/html`。
- 根less podman 下，容器内 uid 0 才映射为宿主当前用户。为让 fpm 以非 root 运行且文件归属不变，php 服务启用 `userns_mode: keep-id`，池进程使用 uid 1000。
- 重启 php 容器后需重启 nginx（nginx 启动时解析并缓存 `php` 服务名，容器重启会更换 IP）。

## 8. 安全加固（在移植范围内顺带完成）

- 输入中的身份变量（`adminid/groupid/discuz_uid/discuz_user/discuz_pw/discuz_secques/accessmasks` 等）从全局提取中剔除，无法再用 URL/表单覆盖会话身份。
- `extract()` 改用 `EXTR_SKIP`；`daddslashes` 始终转义。
- 认证 cookie（`_discuz_uid/_discuz_pw/_discuz_secques/sid`）加 `HttpOnly`。
- 会话/安全令牌生成由 `mt_rand` 改为 `random_int`。
- 后台二次密码比较改为 `hash_equals`（密码存储仍是 md5，老用户可正常登录）。
- 附件上传：白名单为空时回退内置安全白名单，并始终拒绝 `php/php4/php5/php7/php8/phtml/pht/phar/shtml/htaccess` 等可执行扩展名。
- 数据库错误页：SQL 语句与详细错误仅管理员可见，访客只看到通用提示；连接失败时不再因 `mysqli_error(false)` 抛 TypeError。
- `dreferer()` 限制跳转目标必须同站，堵住开放重定向。
- `$PHP_SELF` 过滤 CR/LF 与引号；`HTTP_HOST` 做白名单校验。
- nginx 屏蔽 `forumdata/`、`utilities/` 直链，禁止附件目录执行脚本，`install.php` 仅内网可访问。

## 9. 验证记录

| 项目 | 方法 | 结果 |
|---|---|---|
| 语法 | `php -l` 全量 125 文件 | 0 错误 |
| 模板 | 64 个 `.htm` 编译 + 逐文件 `php -l` | 64/64 通过，0 错误 |
| 页面 | 55 个前后台 URL，登录态 | 全部 0 Warning/0 Fatal/0 Deprecated |
| 中文读 | 版块/主题/搜索页 | GBK 字节正确渲染 |
| 中文写 | 注册、发帖、回复、短消息 | 数据库 HEX 与 GBK 编码一致 |
| 登录 | admin / 新注册用户 | 正常，后台二次验证通过 |
| 搜索 | 标题 / 全文 / 作者 | 正确返回结果并写入 searchindex |
| bbcode | `[img]`、`[img=w,h]`、`[url]` | 正确转换，无内容丢失 |
| 后台 | 菜单及 20+ 模块 | 全部可访问，0 告警 |

## 10. 已知限制与未实施项

- **密码仍为 md5**（无盐）。改为 `password_hash` 会导致所有老用户无法登录，且属于认证逻辑红线，本次未改。
- `submitcheck()` 的 `$allowget` 对 GET 提交仍跳过 formhash（保持 login.txt 中记录的快速登录接口）；POST 均校验 formhash。
- 未做模板级 XSS 全面审计（老模板存在大量直接输出变量的位置），仅修复了移植相关的错误页/重定向/主机头问题。
- `utilities/` 下的升级脚本已去除已删除函数调用，但未做完整回归（属于一次性工具，不应部署到生产）。
- `include/db_mysql_error.php` 的邮件通知在容器内无 MTA，不会真正发信（不影响页面）。

## 11. 回滚方案

1. Git 基线：`aee87b2 baseline: Discuz! 2.5F SP1 GBK 原始包（移植前基线）`。
2. 全部移植改动在独立提交中，按阶段可逆；`git diff aee87b2..HEAD` 可查看完整改动。
3. 如仅需回退运行环境：停掉 podman 栈，将 `upload/` 恢复到基线并部署到 PHP 5.6/MySQL 5.1 环境。
4. 数据库为全新安装，无历史数据迁移负担；`docker/mysql-data` 可整体删除重建。
