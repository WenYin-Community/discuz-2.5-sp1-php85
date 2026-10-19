#!/bin/sh
# 本地邮件捕获器（开发环境用）
# PHP 的 mail() 会以 `capture-mail.sh -t` 调用本脚本并把整封邮件写入 stdin。
# 这里把邮件追加到论坛目录的 forumdata/mail.log，便于本地验证找回密码/通知等功能。
LOG=/var/www/html/forumdata/mail.log
{
  echo "===== $(date '+%Y-%m-%d %H:%M:%S') ====="
  cat
  echo
} >> "$LOG"
exit 0
