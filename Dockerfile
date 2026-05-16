FROM php:8.1-apache

# Cài đặt extension mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Kích hoạt module rewrite của Apache
RUN a2enmod rewrite

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Xóa file index.html mặc định để tránh hiện chữ "It works!"
RUN rm -f /var/www/html/index.html

# Copy toàn bộ mã nguồn vào container
COPY . /var/www/html/

# Phân quyền
RUN chown -R www-data:www-data /var/www/html
