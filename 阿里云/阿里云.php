问题1：
    新购买的ecs实例配置好环境后无法访问80端口
解决办法：
    登陆并进入到ecs实例
    右侧进入更多 - 安全组配置 - 配置规则
    配置开放端口范围 和 可访问的ip
    如配置80端口
    端口配置
     80/80
  ip配置
    0.0.0.0/1 （所有端口可访问）