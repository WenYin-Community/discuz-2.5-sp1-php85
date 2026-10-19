# AGENTS.md

## 项目概览

- Discuz! 2.5F SP1 简体中文版（GBK）论坛，PHP 4 时代代码库，**已移植到 PHP 8.5 + MySQL 5.7**。
- 源码在 `upload/`；根目录 `*.txt` 是 GBK 编码的原始官方文档；`php85-upgrade-report.md` 记录本次移植的全部改动与验证结果。
- 本包带有 `//fix: BY pk0909` 本地补丁，不要回退。
- 不是 Composer/npm 项目，无构建链与自动化测试；版本控制为 Git（基线提交 `aee87b2`）。

## 运行方式

```bash
cd docker && podman-compose up -d      # 启动 php8.5-fpm + mysql5.7 + nginx
# 访问 http://127.0.0.1:8081/ ，MySQL 在 127.0.0.1:3317
cd docker && podman-compose down       # 停止（数据保留在 docker/mysql-data）
podman logs -f discuz25-php            # 查看 PHP 错误日志
```

- **重启 php 容器后必须重启 nginx**（nginx 缓存上游容器 IP，否则 502）。
- 数据库/连接字符集固定为 `gbk`，服务器 `sql_mode=''`；不要改成 utf8，否则存量 GBK 数据会损坏。
- `php.ini` 必须保持 `short_open_tag=On`（模板编译器与部分后台视图使用 `<?`）。
- php-fpm 必须保持 `chdir = /var/www/html`（程序用 `./forumdata/...` 相对路径写缓存）。
- php 服务使用 `userns_mode: keep-id`，池进程 uid 1000 才能写入 `forumdata/` 且文件归属不变。

## 校验命令

```bash
# 语法检查（全部源文件）
podman run --rm -v "$PWD:/repo:ro,Z" --entrypoint sh localhost/wenyinos-php:8.5-fpm \
  -c 'for f in $(find /repo/upload /repo/utilities -name "*.php" -not -path "*/forumdata/*"); do php -l "$f" || exit 1; done'

# 页面冒烟（登录态 cookie 需先通过 logging.php 获取）
curl -s -b /tmp/cj http://127.0.0.1:8081/index.php | iconv -f gb2312 -t utf-8 | grep -cE "Warning|Fatal error"

# 清空缓存强制重建（删除后首次访问自动生成）
rm -f upload/forumdata/cache/*.php upload/forumdata/templates/*.tpl.php
```

无单元测试；改动后的最低验证：全量 `php -l` + 前后台关键页面冒烟 + 发帖/回复中文不乱码。

## 目录与分层

- `upload/*.php`：前台入口，一文件一功能；入口先定义 `CURRSCRIPT`，再 `require './include/common.php'`。
- `upload/include/common.php`：引导核心（配置、DB、会话、用户组、风格、缓存自动重建、输入提取）。
- `upload/include/global.php`：函数库（`showmessage`、`template`、`submitcheck`、`transsid`、`dhtmlspecialchars`、`dinterpolate`、`random`）。
- `upload/include/db_mysql.php`：**mysqli 版** `dbstuff` 类，全站唯一数据库访问层。
- `upload/include/template.php`：模板编译器（`/e` 已全部改为 `preg_replace_callback`）。
- `upload/include/discuzcode.php`：BBCode 解析（含 `[code]/[php]/[hide]/[img]/[url]` 回调）。
- `upload/include/cache.php`：缓存生成器；`forumdata/cache/*.php` 与 `forumdata/templates/*.tpl.php` 都是生成物，不要手改。
- `upload/admin/`：后台业务；`upload/admin/tpl/`：后台视图。
- `upload/templates/default/`：默认模板（`*.htm`）与语言包（`*.lang.php`）。
- `utilities/`：一次性升级/修复脚本，不属于部署内容，用完删除。

## 编码与编辑规则

- **所有原始 PHP/HTM 文件是 GB2312/GBK + CRLF**。新增中文必须写回 GBK，不要转 UTF-8。
- 读取乱码文件：`iconv -f gb2312 -t utf-8 <file>`；写回：`iconv -f utf-8 -t gb2312`。
- 修改源码时优先用字节安全的脚本（Python 以 `gbk`/`surrogateescape` 读写），不要用会按 UTF-8 保存的编辑器直接改中文。
- 页面继续声明 `charset=gb2312`；表单按 GBK 提交，数据库 HEX 应与 GBK 编码一致。

## 代码约定（PHP 8 下仍保持 PHP 4 风格）

- 全局变量、无命名空间/类型声明；匹配现有风格，不要顺手现代化。
- 用户输入经 `extract()` 成为裸变量；SQL 拼接用 `intval()`/`addslashes()`，HTML 输出用 `dhtmlspecialchars()`（**不要用裸 `htmlspecialchars()`**，PHP 8 默认 UTF-8 会把 GBK 字节变成空串）。
- 改状态的 POST 表单必须带 `formhash` 并调用 `submitcheck()`；前台提示用 `showmessage()`，后台用 `cpmsg()`。
- `include/` 下文件保留 `IN_DISCUZ` 守卫；后台脚本保留 `IN_ADMINCP` 守卫。
- 数据库只用 `$db->query()/fetch_array()/result()/num_rows()/insert_id()/affected_rows()`；表名用 `$table_<name>` 全局（前缀 `$tablepre`）。
- `mysqli_report(MYSQLI_REPORT_OFF)` 必须保留（代码依赖“查询失败返回 false”）。

## 移植后的关键陷阱

- **不要预置 `action`、`searchid`、`fid`、`previewpost`、`showattach`、`tids`、`change`、`expand` 这些全局变量**：代码用 `isset()` 区分“是否提交/是否存在”，预置会改变分支行为。
- `$forum` 只在“已经是数组”时补字段；不要把它从 `false` 改成 `array()`，否则 `if($tid && $forum)`、`if($forum)` 这类真值判断会失效。
- `include/cache.php` 的 `array_del()` 现在**有意为空实现**：原版 `trim($f)`（`$f` 是数组）在 PHP 5/7 下恒为假，从未删除任何键，而 `welcomemsg/bbrules/bbrulestxt` 依赖它们留在缓存中。
- 模板条件里写变量必须带 `$`（如 `$allowdelpost`）；裸标识符在 PHP 8 下是未定义常量，会致命。
- 字符串插值中的链式下标必须写成 `{$a[$b]['c']}`；`"$a[$b][c]"` 中的 `[c]` 会被当作字面文本。
- 稀疏数组（`array($value => 'checked')`）在模板里访问其他键会告警；按模板实际键集合预填充。
- `dinterpolate()` 取代了语言包的 `eval()`；它支持裸键，但不会转换 `{LF}`（与原行为一致）。
- 密码仍是 md5（兼容老用户）；后台二次密码比较已用 `hash_equals()`。
- 附件上传目录禁止执行脚本（nginx 已配置），上传白名单为空时回退内置白名单。
- **访问后台首页（`admincp.php?action=home`）会自动删除源码目录里的 `install.php`**（`admin/home.php` 的安全特性）。冒烟测试后台后若发现 `install.php` 消失，用 `git checkout HEAD -- upload/install.php` 恢复；生产部署则应在安装完成后主动删除它。

## 改动前先读

- `php85-upgrade-report.md`：移植清单、验证结果、已知限制。
- `CHANGELOG.md`：本次与后续变更记录。
- `docker/README.md`：容器启停、重置数据库、排查。
- `template.txt`、`plugin.txt`、`language.txt`、`files.txt`、`upgrade.txt`：GBK 官方文档，阅读前 `iconv`。
- 涉及权限、认证、安装、缓存时，先读 `include/common.php`、`include/global.php`、`include/db_mysql.php`、`include/cache.php`、`admin/global.php`、`install.php`。
