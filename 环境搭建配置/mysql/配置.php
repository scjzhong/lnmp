阿里云新建实例外网无法访问的问题。
    在使用了一下操作
        update user set host='%' where user ='root';
        flush privileges;
                添加mysql安全组
                检查my.cnf文件bind_address=0.0.0.0
                查看防火墙是否关闭
        systemctl status firewalld
            ● firewalld.service - firewalld - dynamic firewall daemon
           Loaded: loaded (/usr/lib/systemd/system/firewalld.service; disabled; vendor preset: enabled)
           Active: inactive (dead)
             Docs: man:firewalld(1)
        
                各种重启mysql
                检查3306端口是否在监听
        netstat -tln
            tcp        0      0 0.0.0.0:3306            0.0.0.0:*               LISTEN     
            tcp        0      0 0.0.0.0:80              0.0.0.0:*               LISTEN     
            tcp        0      0 0.0.0.0:22              0.0.0.0:*               LISTEN
            
            最后外网还是无法访问mysql
            最后
            iptables -L
            
            Chain INPUT (policy ACCEPT)
            target     prot opt source               destination         
            ACCEPT     all  --  anywhere             anywhere            
            ACCEPT     all  --  anywhere             anywhere             state RELATED,ESTABLISHED
            ACCEPT     all  --  anywhere             anywhere            
            ACCEPT     tcp  --  anywhere             anywhere             tcp dpt:http
            ACCEPT     tcp  --  anywhere             anywhere             tcp dpt:https
            DROP       tcp  --  anywhere             anywhere             tcp dpt:mysql
            ACCEPT     icmp --  anywhere             anywhere             icmp echo-request
            发现
            DROP       tcp  --  anywhere             anywhere             tcp dpt:mysql
            然后执行
            iptables -R INPUT 6 -j ACCEPT
            即可外网访问了。      
            
            