redis 相关

1：redis相关的安装以及数据结构这里就不多说较为简单。

2：redis的事务
        这里首先复习下set 和setnx 
        set是不论 key是否存在都去执行。
        
        127.0.0.1:6380[2]> del name
        (integer) 1
        127.0.0.1:6380[2]> set name scjzhong
        OK
        127.0.0.1:6380[2]> get name
        "scjzhong"
        127.0.0.1:6380[2]> set name scjzhong1
        OK
        127.0.0.1:6380[2]> get name
        "scjzhong1"
        127.0.0.1:6380[2]> 
        
        上面的是set相关的这里没有什么问题。
        我们来和setnx进行比较
        127.0.0.1:6380[2]> del name
        (integer) 0
        127.0.0.1:6380[2]> setnx name scjzhong
        (integer) 1
        127.0.0.1:6380[2]> get name
        "scjzhong"
        127.0.0.1:6380[2]> setnx name scjzhong1
        (integer) 0
        127.0.0.1:6380[2]> get name
        "scjzhong"
        127.0.0.1:6380[2]> 

        setnx 当且仅当 key 不存在时去设置 key 存在时则不去设置
        
        下面是事务相关的介绍
        redis提供类类似关系型数据库的事务。
                 提供了以下的3个命令
        multi    <-> (begin transaction) 
        exec     <-> commit
        discard  <-> rollback
                可以这样理解
              multi（开启事务）
              ……
              ……
              ……  
              exec （成功提交事务）
                ……
              discard （失败回滚事务）
              
     提交         
        127.0.0.1:6380[2]> keys *
        (empty list or set)
        127.0.0.1:6380[2]> multi
        OK
        127.0.0.1:6380[2]> set name scjzhong
        QUEUED
        127.0.0.1:6380[2]> set age 10
        QUEUED
        127.0.0.1:6380[2]> get name
        QUEUED
        127.0.0.1:6380[2]> get age
        QUEUED
        127.0.0.1:6380[2]> exec
        1) OK
        2) OK
        3) "scjzhong"
        4) "10"
        127.0.0.1:6380[2]> get name
        "scjzhong"
        127.0.0.1:6380[2]> get age
        "10"
        127.0.0.1:6380[2]> 
        
        
        
            回滚
        127.0.0.1:6380[2]> keys *
        (empty list or set)
        127.0.0.1:6380[2]> multi
        OK
        127.0.0.1:6380[2]> set name scjzhong
        QUEUED
        127.0.0.1:6380[2]> set age 10
        QUEUED
        127.0.0.1:6380[2]> discard
        OK
        127.0.0.1:6380[2]> get name
        (nil)
        127.0.0.1:6380[2]> get age
        (nil)
        127.0.0.1:6380[2]> 
        
        
3：redis的持久化
    Redis 提供了多种不同级别的持久化方式：rdb 和 aof
         详见
    http://doc.redisfans.com/topic/persistence.html
    
                
                
                
                
                
                
                
                
                
                
                
       