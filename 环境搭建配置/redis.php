redis ���

1��redis��صİ�װ�Լ����ݽṹ����Ͳ���˵��Ϊ�򵥡�

2��redis������
        �������ȸ�ϰ��set ��setnx 
        set�ǲ��� key�Ƿ���ڶ�ȥִ�С�
        
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
        
        �������set��ص�����û��ʲô���⡣
        ��������setnx���бȽ�
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

        setnx ���ҽ��� key ������ʱȥ���� key ����ʱ��ȥ����
        
        ������������صĽ���
        redis�ṩ�����ƹ�ϵ�����ݿ������
                 �ṩ�����µ�3������
        multi    <-> (begin transaction) 
        exec     <-> commit
        discard  <-> rollback
                �����������
              multi����������
              ����
              ����
              ����  
              exec ���ɹ��ύ����
                ����
              discard ��ʧ�ܻع�����
              
     �ύ         
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
        
        
        
            �ع�
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
        
        
3��redis�ĳ־û�
    Redis �ṩ�˶��ֲ�ͬ����ĳ־û���ʽ��rdb �� aof
         ���
    http://doc.redisfans.com/topic/persistence.html
    
                
                
                
                
                
                
                
                
                
                
                
       