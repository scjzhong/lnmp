一：针对单机优化。
    1：提高页面的访问速度
                减少页面的大小启用nginx 的gzip压缩
        #开启gzip 压缩
        gzip  on;
        #gzip 压缩的最小的尺寸 大于 1k 才进行压缩
        gzip_min_length 1k;
        #缓存的缓冲区单位（暂时可不开）
        gzip_buffers 4 16k;
        #压缩级别，1-10，数字越大压缩的越好，时间也越长
        gzip_comp_level 6;
        #对以下的文件类型进行压缩
        gzip_types text/plain application/javascript text/css application/xml;
        
                开启后 重启nginx 在访问文件会发现文件大小 会变小
                
    2：减少资源的请求的数量，合并和压缩css js
                使用minfy
                把css 和 js 压缩和削减（去掉空格回车符等） 以及把多个文件整合到一个文件里
                这样做的意义在于是文件的合并而 不是压缩。可以减少浏览器不断发出的新的连接
                如ftp文件服务器一样，多个文件和一个大文件耗时是 不一样的。
                
    3：设置浏览器缓存
        #开启浏览器缓存
        location ~.*\.(js|css|jpg|jpeg|gif|png)$ {#指定缓存文件类型
            #设置浏览器过期时间
            expires 7d;
        }
            注意 该部分配置只能配置在server中
            在server中配置时重启nginx 可能会出现文件 404 那是因为配置好后找不到项目根目录
       
        location / {
            root   /home/wwwroot/static/;  
            index  index.php index.html index.htm;
        }
               修改成如下即可        
        root   /home/wwwroot/static/;  
        location / {  
            index  index.php index.html index.htm;
        }
            
            重启后会在Response Headers中会有
      Expires:Fri, 09 Feb 2018 16:22:10 GMT #过期时间
      Last-Modified:Sat, 05 Aug 2017 05:28:11 GMT #最后修改时间
      
            在资源上 原size中有原来的 5k  直接变成 from memory cache 表明请求是浏览器的缓存
            在请求头上
            Status Code:200 OK (from memory cache) 也表明请求是浏览器的缓存
               
    
        
        