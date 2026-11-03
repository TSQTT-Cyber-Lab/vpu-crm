# VPU-CRM demo — Ubuntu 24.04 + Apache + PHP 8.3 (SuiteCRM 7.15 hỗ trợ PHP 8.1–8.4).
# Build context = thư mục gốc repo (xem docker-compose.yml).
FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive TZ=Asia/Ho_Chi_Minh
RUN apt-get update \
 && apt-get install -y --no-install-recommends apache2 libapache2-mod-php8.3 php8.3-cli php8.3-mysql php8.3-gd \
      php8.3-intl php8.3-zip php8.3-mbstring php8.3-xml php8.3-curl php8.3-bcmath php8.3-soap php8.3-imap php8.3-opcache \
      mariadb-client curl ca-certificates tzdata \
 && a2enmod rewrite headers expires \
 && ln -sf /dev/stdout /var/log/apache2/access.log && ln -sf /dev/stderr /var/log/apache2/error.log \
 && rm -rf /var/lib/apt/lists/* /var/www/html/index.html

COPY 09_DEMO/docker/php.ini /etc/php/8.3/apache2/conf.d/99-vpu-demo.ini
COPY 09_DEMO/docker/php.ini /etc/php/8.3/cli/conf.d/99-vpu-demo.ini
COPY 09_DEMO/docker/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY suitecrm/ /var/www/html/
COPY 09_DEMO/docker/entrypoint.sh 09_DEMO/docker/setup.sh /opt/vpu-demo/
COPY 09_DEMO/seed/ /opt/vpu-demo/seed/

# Checkout trên Windows có thể đổi .sh sang CRLF -> chuẩn hoá lại LF.
RUN sed -i 's/\r$//' /opt/vpu-demo/*.sh && chmod +x /opt/vpu-demo/*.sh \
 && rm -f /var/www/html/config.php /var/www/html/config_override.php /var/www/html/config_si.php \
 && chown -R www-data:www-data /var/www/html

EXPOSE 80
ENTRYPOINT ["/opt/vpu-demo/entrypoint.sh"]
