# Discuz! 2.5 本地运行栈（podman）

PHP 8.5（php-fpm）+ MySQL 5.7 + nginx，根less podman 运行。

## 启动 / 停止

```bash
cd docker
podman-compose up -d          # 启动
podman-compose down           # 停止（保留数据库数据）
podman-compose down -v        # 停止并删除容器（./mysql-data 仍在宿主）
```

访问：<http://127.0.0.1:8081/>

## 端口与凭据

| 项目 | 值 |
|---|---|
| Web | `127.0.0.1:8081` |
| MySQL | `127.0.0.1:3317` |
| 数据库 | `MYSQL_DATABASE`（字符集 `gbk`） |
| 应用账号 | `MYSQL_USER` / `MYSQL_PASSWORD` |
| root 密码 | `MYSQL_ROOT_PASSWORD` |

数据库凭据放在 `docker/.env`（由 `.env.example` 复制后填写，**不纳入版本控制**）。
安装向导中的数据库主机填 `mysql`，库名/用户/密码填 `.env` 中的值。`upload/config.php` 由安装程序写入。

## 首次安装

容器启动后，浏览器访问 `http://127.0.0.1:8081/install.php`（nginx 仅允许内网访问）。安装程序会写入 `config.php` 并建表。

也可以直接使用已初始化的数据库（`docker/mysql-data` 目录保留了当前数据）。

## 重置数据库

```bash
cd docker
podman-compose down
rm -rf mysql-data
podman-compose up -d
# 然后重新访问 install.php
```

或在容器内执行 SQL：

```bash
podman exec -i discuz25-mysql mysql -uroot -p discuz25 < dump.sql   # 按提示输入 root 密码
```

## 常用排查

```bash
podman ps --format '{{.Names}} {{.Status}}'
podman logs -f discuz25-php
podman logs -f discuz25-nginx
podman logs -f discuz25-mysql
```

- **修改代码后**：无需重启，`$tplrefresh = 1` 会自动重编译模板。若修改了语言包，请到后台“更新缓存”。
- **重启 php 容器后必须重启 nginx**：nginx 启动时解析 `php` 上游地址并缓存，容器 IP 变化会导致 502。
- **访问后台首页会自动删除 `install.php`**：这是 Discuz 自带的安全特性（`admin/home.php`）。若仍需安装程序，用 `git checkout HEAD -- upload/install.php` 恢复；生产环境安装完成后本来就应该删除它。
- **缓存写不进去**：确认 `upload/forumdata/`、`cache/`、`templates/` 可由 uid 1000 写入（`userns_mode: keep-id` 已处理归属）。
- **中文乱码**：确认数据库/连接字符集为 `gbk`（`docker/php-fpm.conf` 与 `podman-compose.yml` 已固定）。

## 目录说明

- `podman-compose.yml`：三个服务定义。
- `php.ini`：`short_open_tag=On`（模板编译器输出短标签）、错误显示、上传限制。
- `php-fpm.conf`：池用户（uid 1000）与 `chdir=/var/www/html`（程序依赖相对路径写缓存）。
- `nginx.conf`：反代、附件防执行、`forumdata/`+`utilities/` 屏蔽、`install.php` 内网限制。
- `mysql-data/`：MySQL 数据目录（已在 `.gitignore` 中忽略）。
