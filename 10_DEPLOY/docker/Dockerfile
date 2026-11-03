# VPU-CRM — image PRODUCTION: Ubuntu 24.04 + Apache + PHP 8.3 (SuiteCRM 7.15 hỗ trợ PHP 8.1–8.4).
# Build context = thư mục gốc repo:  cd 10_DEPLOY/docker && docker compose build
# Khác bản demo (09_DEMO): không có dữ liệu giả / tài khoản demo; cấu hình qua .env; có dịch vụ cron; log + file đính kèm
# + cấu hình nằm trong volume /state.
FROM ubuntu:24.04

ARG VPU_VERSION=dev
LABEL org.opencontainers.image.title="VPU-CRM" \
      org.opencontainers.image.description="CRM tuyển sinh Trường Đại học Victoria Thái Bình Dương (dựa trên SuiteCRM 7.15, AGPLv3)" \
      org.opencontainers.image.version="${VPU_VERSION}" \
      org.opencontainers.image.licenses="AGPL-3.0-only"

ENV DEBIAN_FRONTEND=noninteractive TZ=Asia/Ho_Chi_Minh VPU_VERSION=${VPU_VERSION}
RUN apt-get update \
 && apt-get install -y --no-install-recommends apache2 libapache2-mod-php8.3 php8.3-cli php8.3-mysql php8.3-gd \
      php8.3-intl php8.3-zip php8.3-mbstring php8.3-xml php8.3-curl php8.3-bcmath php8.3-soap php8.3-imap php8.3-opcache \
      mariadb-client curl ca-certificates tzdata \
 && a2enmod rewrite headers expires remoteip \
 && ln -sf /dev/stdout /var/log/apache2/access.log && ln -sf /dev/stderr /var/log/apache2/error.log \
 && rm -rf /var/lib/apt/lists/* /var/www/html/index.html

COPY 10_DEPLOY/docker/rootfs/php.ini /etc/php/8.3/apache2/conf.d/99-vpu.ini
COPY 10_DEPLOY/docker/rootfs/php.ini /etc/php/8.3/cli/conf.d/99-vpu.ini
COPY 10_DEPLOY/docker/rootfs/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY 10_DEPLOY/docker/rootfs/security.conf /etc/apache2/conf-available/vpu-security.conf
COPY suitecrm/ /var/www/html/
COPY 10_DEPLOY/docker/rootfs/*.sh /opt/vpu/

# Checkout trên Windows có thể đổi .sh sang CRLF -> chuẩn hoá LF. Bỏ file cấu hình/test khỏi image.
RUN sed -i 's/\r$//' /opt/vpu/*.sh && chmod +x /opt/vpu/*.sh \
 && a2enconf vpu-security \
 && rm -f /var/www/html/config.php /var/www/html/config_override.php /var/www/html/config_si.php \
 && rm -rf /var/www/html/custom/include/VPU/*/tests \
 && echo "${VPU_VERSION}" > /var/www/html/VPU_VERSION.txt \
 && chown -R www-data:www-data /var/www/html

EXPOSE 80
ENTRYPOINT ["/opt/vpu/entrypoint.sh"]
CMD ["web"]
