# Changelog

本文件记录 Discuz! 2.5F SP1 GBK 分支的变更。

## [2.5F SP1 / 2026-10-20] PHP 8.5 + MySQL 5.7 移植

### 运行环境

- 目标环境改为 PHP 8.5（php-fpm）+ MySQL 5.7 + nginx，提供 `docker/` podman 容器栈。
- 保持 GBK（GB2312）编码与 `charset=gb2312` 输出不变。

### PHP 8 兼容性（阻断项）

- `include/db_mysql.php` 由 `mysql_*` 重写为 mysqli，保留原 `dbstuff` 全部 API；连接后 `SET NAMES 'gbk'`；关闭 mysqli 异常模式；查询失败时 `fetch_array` 返回 `FALSE`。
- 移除 `set_magic_quotes_runtime()` / `get_magic_quotes_gpc()`；`daddslashes` 对空数组不再返回 `false`（修复每个空 POST 请求的 `extract(): TypeError`）。
- 模板编译器、`output()`、`discuzcode.php` 中 16 条 `preg_replace /e` 全部改为 `preg_replace_callback`。
- 841 处裸数组键 `$var[key]` 改为 `$var['key']` / `{$var['key']}`；3 处嵌套花括号插值改为合法写法。
- 28 处裸常量 `case settings:` 等改为字符串；模板中 `allowdelpost` 补 `$`。
- `count()/array_merge()` 非数组入参补守卫（14 处）；`array_del()` 显式保留原有“不删除”行为。
- `fsockopen(..., &$errno, ...)` 去掉调用时引用；PHP 8.5 的 `(double)` 改为 `(float)`。
- 语言包 `eval()` 插值改为新增的 `dinterpolate()`。
- `register.php` 的 `$$setting['variable']` 改为 `${$setting['variable']}`。
- 修复 `install.php` 中 `$query - $db->query()` 笔误。

### 告警治理

- 预置请求级变量并按 `isset()` 语义避开 `action/searchid/fid/previewpost` 等变量。
- `$forum` 仅在为数组时补齐字段，保持 `false` 的原真值语义。
- 稀疏数组（时区、每页帖数、排序、高亮等）按模板键集合预填充。
- 全站 55 个前后台页面/动作组合达到 0 Warning / 0 Fatal / 0 Deprecated。

### MySQL 5.7

- `cdb_caches` 的 `TYPE=HEAP` 改为 `ENGINE=InnoDB`（MEMORY 不支持 TEXT）。
- 建库/建表使用 `gbk` 字符集；服务器 `sql_mode=''`。
- 移除 `SQL_CACHE` 注入（5.7 弃用、8.0 移除）。

### 安全加固

- 输入中剔除身份变量，`extract()` 使用 `EXTR_SKIP`，阻止 URL/表单覆盖会话身份。
- 认证 cookie 加 `HttpOnly`；令牌改用 `random_int()`；后台密码比较改用 `hash_equals()`（密码存储仍为 md5）。
- 附件上传增加安全白名单回退与危险扩展名拒绝。
- 数据库错误页仅管理员可见 SQL/错误详情。
- `dreferer()` 限制同站跳转；`HTTP_HOST`/`PHP_SELF` 过滤。
- nginx 屏蔽 `forumdata/`、`utilities/`，禁止附件目录执行脚本，`install.php` 仅内网可访问。

### 显示与部署修复

- 修复中文显示为方块/替换字符：PHP 默认 `default_charset=UTF-8` 导致响应头与 GBK 正文不一致。`config.php` 的 `$headercharset` 改为 1，并在 `docker/php.ini` 设置 `default_charset = gb2312`。
- 修复初始数据编码：安装 SQL 必须以 GBK 字节导入，否则 `游客/管理员` 等初始文本会以 UTF-8 字节写入 gbk 库。
- 修复 `install.php` 源码泄露：nginx 精确匹配 location 需显式配置 fastcgi，否则 PHP 源码会被当作静态文件下载。

### 部署提示

- 覆盖部署时请勿覆盖 `config.php` 与 `forumdata/`（缓存、编译模板、日志）。
- 迁移到新环境后删除 `forumdata/cache/*` 与 `forumdata/templates/*`，首次访问会自动重建。
- 重启 php 容器后需同步重启 nginx（上游 IP 缓存）。
