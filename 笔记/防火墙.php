防火墙相关
    以下基于centos 7
    systemctl start firewalld
        查看防火墙的版本
    firewall-cmd --version 
        查看运行状态
    firewall-cmd --stat
        获取所有的区域配置情况
    firewall-cmd --list-all-zone
    查询某个服务是否可用
    firewall-cmd --query-service=ssh
    
    
  查看端口如开启防火墙后
    http://118.10.22.125:8080/ 开启防火墙后该端口不能访问
    此时添加该端口
    firewall-cmd --add-port=8080/tcp
    添加后即可访问
    
    删除
    firewall-cmd --remove-port=8080/tcp
    8080端口删除后监听该端口即无法访问了
    
    查看开启的服务
    firewall-cmd --list-service
    查看开启的端口
    firewall-cmd --list-port
    开放3306端口
    firewall-cmd --add-port=3306/tcp
    
    开放6379端口
    firewall-cmd --add-port=6379/tcp
    