FROM wordpress:php8.2-apache

# ステータスボード画面を配置
COPY index.php /var/www/html/status.php
COPY index.php /var/www/html/index.php

# Apache の設定権限を調整
RUN chown -R www-data:www-data /var/www/html