https://www.cnblogs.com/duanweishi/p/6151721.html

// 数据表
1.创建数据表文件：php artisan make:migration create_images_table --create=images
2.修改database->migrations->create_images_table文件
3.创建库迁移：php artisan migrate
4.数据库回迁：php artisan migrate:reset

// nginx

1.配置目录/etc/nginx/nginx.conf
2.日志目录/var/log/nginx/error.log
4.命令行service nginx {start|stop|restart}
5.nginx.conf
    server {
        listen 8080;
        server_name www.cyd1010.top;
        root /home/projects/lumen-auth/public/;
        index  index.php index.html index.htm;
        // php
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }
        // 静态目录
        location /images/ {
            root /home/projects/lumen-auth/public/;
        }
        // php配置
        location ~ \.php$ {
            try_files $uri /index.php =404;
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
        ssl on;
        ssl_certificate   cert/1525030857167.pem;
        ssl_certificate_key  cert/1525030857167.key;
        ssl_session_timeout 5m;
        ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
        ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
        ssl_prefer_server_ciphers on;
    }

// php
1.需要启动php-fpm

// mysql
1.登录：mysql -u root -p
2.查看数据库： show databases;
3.查看数据表： show tables；
4.进入数据库：use “database name”;
5.刷新： flush privileges;
