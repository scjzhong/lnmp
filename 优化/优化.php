һ����Ե����Ż���
    1�����ҳ��ķ����ٶ�
                ����ҳ��Ĵ�С����nginx ��gzipѹ��
        #����gzip ѹ��
        gzip  on;
        #gzip ѹ������С�ĳߴ� ���� 1k �Ž���ѹ��
        gzip_min_length 1k;
        #����Ļ�������λ����ʱ�ɲ�����
        gzip_buffers 4 16k;
        #ѹ������1-10������Խ��ѹ����Խ�ã�ʱ��ҲԽ��
        gzip_comp_level 6;
        #�����µ��ļ����ͽ���ѹ��
        gzip_types text/plain application/javascript text/css application/xml;
        
                ������ ����nginx �ڷ����ļ��ᷢ���ļ���С ���С
                
    2��������Դ��������������ϲ���ѹ��css js
                ʹ��minfy
                ��css �� js ѹ����������ȥ���ո�س����ȣ� �Լ��Ѷ���ļ����ϵ�һ���ļ���
                �������������������ļ��ĺϲ��� ����ѹ�������Լ�����������Ϸ������µ�����
                ��ftp�ļ�������һ��������ļ���һ�����ļ���ʱ�� ��һ���ġ�
                
    3���������������
        #�������������
        location ~.*\.(js|css|jpg|jpeg|gif|png)$ {#ָ�������ļ�����
            #�������������ʱ��
            expires 7d;
        }
            ע�� �ò�������ֻ��������server��
            ��server������ʱ����nginx ���ܻ�����ļ� 404 ������Ϊ���úú��Ҳ�����Ŀ��Ŀ¼
       
        location / {
            root   /home/wwwroot/static/;  
            index  index.php index.html index.htm;
        }
               �޸ĳ����¼���        
        root   /home/wwwroot/static/;  
        location / {  
            index  index.php index.html index.htm;
        }
            
            ���������Response Headers�л���
      Expires:Fri, 09 Feb 2018 16:22:10 GMT #����ʱ��
      Last-Modified:Sat, 05 Aug 2017 05:28:11 GMT #����޸�ʱ��
      
            ����Դ�� ԭsize����ԭ���� 5k  ֱ�ӱ�� from memory cache ����������������Ļ���
            ������ͷ��
            Status Code:200 OK (from memory cache) Ҳ����������������Ļ���
               
    
        
        