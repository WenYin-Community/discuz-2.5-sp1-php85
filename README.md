# Discuz! 2.5F SP1 GBK · PHP 8.5 + MySQL 5.7

[English](#english) | 简体中文

Discuz! 2.5F SP1（2005 年发布的经典中文论坛程序）的现代化移植分支：在**完整保留 GBK 编码与老式浏览器兼容性**的前提下，运行于 **PHP 8.5（php-fpm）+ MySQL 5.7**，并顺带完成一轮安全加固。

本仓库不是重写版，也不新增前端功能；所有改动都用于消除 PHP 8 / MySQL 5.7 的不兼容点与历史安全问题。

## 亮点

- **PHP 8.5 兼容**：`mysql_*` → mysqli、全部 `preg_replace /e` → 回调、841 处裸数组键、28 处裸 `case` 常量、`extract(null)`、`count()`/`array_merge()` 非数组入参等致命项已修复。
- **MySQL 5.7 兼容**：`TYPE=HEAP` → InnoDB、全库 `gbk` 字符集、`sql_mode=''`、移除 `SQL_CACHE`。
- **编码不变**：源码保持 GB2312/GBK + CRLF，页面 `charset=gb2312`，数据库与连接均为 `gbk`；中文读写经过字节级校验。
- **老式浏览器优先**：不引入任何现代前端框架，不要求 JavaScript；原有模板、WML/WAP 入口与 BBCode 行为保持。
- **安全加固**：密码由 md5 升级为 `password_hash`（历史 md5 登录后自动升级）、CSRF 令牌（GET/POST 均校验）、身份变量不可注入、HttpOnly Cookie、`random_int` 令牌、上传白名单兜底、数据库错误页仅管理员可见 SQL、后台框架页反射型 XSS 修复、同站跳转限制。
- **可复现运行环境**：`docker/` 提供 podman 栈（php 8.5-fpm + MySQL 5.7 + nginx）。

## 环境要求

| 组件 | 版本 |
|---|---|
| PHP | 8.5（mysqli、mbstring、iconv、gd、zlib；`short_open_tag=On`） |
| MySQL | 5.7（服务端字符集 `gbk`，`sql_mode=''`） |
| Web | nginx（或 Apache + php-fpm） |
| 容器 | podman + podman-compose（可选，推荐） |

## 快速开始（podman）

```bash
cd docker
cp .env.example .env          # 按需修改数据库密码
podman-compose up -d          # 启动 php-fpm / mysql / nginx
```

然后打开 <http://127.0.0.1:8081/install.php>，按向导填写：

- 数据库主机：`mysql`
- 数据库名 / 用户 / 密码：`docker/.env` 中的值
- 表前缀：`cdb_`
- 管理员账号：自行设置（密码将使用 bcrypt 存储）

安装完成后删除 `install.php`；后台首页也会自动删除它（Discuz 自带的安全特性）。

> 重启 php 容器后请同步重启 nginx（nginx 启动时缓存上游容器 IP）。

不使用容器时：把 `upload/` 内容部署到 Web 根目录，创建 `config.php`（安装向导可代写），确保 `templates/`、`attachments/`、`customavatars/`、`forumdata/`、`forumdata/cache/`、`forumdata/templates/`、`forumdata/accesslogs/` 可写。

## 目录结构

```
upload/                 可部署的程序根目录
  *.php                 前台入口（index / forumdisplay / viewthread / post / ...）
  include/              核心库：引导、数据库、模板编译、缓存、BBCode
  admin/                后台业务；admin/tpl/ 为后台视图
  templates/default/    默认模板（*.htm）与语言包（*.lang.php）
  forumdata/            运行时数据：缓存、编译模板、日志（不纳入版本控制）
  attachments/          附件目录（不纳入版本控制）
utilities/              一次性升级/修复工具（不应部署到生产）
docker/                 podman 运行栈与说明
php85-upgrade-report.md 移植清单、验证记录与遗留问题处理
CHANGELOG.md            变更记录
AGENTS.md               面向后续维护者/Agent 的项目约定与陷阱
```

## 兼容性与运维要点

- **GBK**：源码为 GB2312/GBK + CRLF；新增中文必须写回 GBK，切勿转 UTF-8。数据库/连接字符集必须为 `gbk`。
- **HTTP 头**：`config.php` 的 `$headercharset = 1` 与 `php.ini` 的 `default_charset = gb2312` 必须保留，否则浏览器会按 UTF-8 解码 GBK 正文而出现方块。
- **短标签**：模板编译器会输出 `<?`，`short_open_tag` 必须为 `On`。
- **工作目录**：php-fpm 需设置 `chdir` 到站点根目录（程序使用 `./forumdata/...` 相对路径写缓存）。
- **缓存**：`forumdata/cache/` 与 `forumdata/templates/` 是生成物，删除后首次访问会自动重建；修改 `*.lang.php` 后需在后台“更新缓存”。
- **邮件**：容器内没有 MTA，`docker/capture-mail.sh` 会把 `mail()` 的邮件写入 `forumdata/mail.log`，便于本地验证找回密码等功能。

## 安全说明

本分支修复了移植过程中确认的安全问题，但**不构成完整的安全审计**：这是 2005 年的代码库，仍可能存在未发现的注入、XSS 或文件处理问题。公网部署前请自行做渗透测试，并至少完成：

1. 修改管理员密码与数据库密码，删除 `install.php`、不要部署 `utilities/`；
2. Web 层屏蔽 `forumdata/`、`utilities/` 直链，禁止附件目录执行脚本（`docker/nginx.conf` 已提供示例）；
3. 使用 HTTPS，限制后台访问来源，定期备份数据库与附件。

## 验证状态

- 125 个 PHP 源文件 `php -l` 全部通过；64 个模板编译成功且产物 lint 通过。
- 安装向导（语言 → 配置 → 环境检查 → 创建管理员）全程 0 告警，建出 35 张表。
- 前台/后台 29 个页面与动作在登录态下 0 Warning / 0 Fatal / 0 Deprecated。
- 中文写入（注册、发帖、回复、短消息）与数据库 HEX 逐字节比对通过；BBCode（`[img]` / `[img=w,h]` / `[url]`）渲染正常。
- 详细记录见 [php85-upgrade-report.md](php85-upgrade-report.md)。

## 相关文档

- [php85-upgrade-report.md](php85-upgrade-report.md) — 移植清单、环境差异、验证记录、遗留问题处理
- [CHANGELOG.md](CHANGELOG.md) — 变更记录
- [AGENTS.md](AGENTS.md) — 代码约定、已知陷阱、校验命令
- [docker/README.md](docker/README.md) — 容器启停、重置数据库、排查
- 根目录 `*.txt` — Discuz! 原始官方文档（GBK 编码，建议 `iconv -f gb2312 -t utf-8` 阅读）

## 许可证与致谢

本仓库的移植与改造代码以 [MIT License](LICENSE) 发布。

原始 Discuz! 2.5 程序由 **Comsenz Technology Ltd（康盛创想）** 开发，版权归其所有；原始授权条款见仓库根目录的 [license.txt](license.txt)。本项目与 Comsenz 官方无关。

维护：[WenYin Community](https://github.com/WenYin-Community)

---

## English

A community-maintained port of **Discuz! 2.5F SP1 GBK** (a classic Chinese BBS from 2005) to **PHP 8.5 + MySQL 5.7**, with the original GBK encoding and legacy-browser compatibility preserved. The changes are limited to compatibility and security fixes — no new frontend features.

Highlights: mysqli database layer, all `preg_replace /e` replaced by callbacks, PHP 8 fatal/warning cleanup, MySQL 5.7 schema/charset fixes, bcrypt password hashing with transparent md5 upgrade, enforced CSRF tokens, HttpOnly cookies, upload extension allowlist, and a podman-based reproducible stack.

Quick start: `cd docker && cp .env.example .env && podman-compose up -d`, then open `http://127.0.0.1:8081/install.php`.

See [php85-upgrade-report.md](php85-upgrade-report.md) for the full porting report.

Licensed under the MIT License for this repository's modifications; the original Discuz! code remains copyright Comsenz Technology Ltd (see `license.txt`).
