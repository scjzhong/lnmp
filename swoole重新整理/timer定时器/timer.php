1：简易定时器
<?php

//每隔2000ms触发一次 直到进程结束
swoole_timer_tick(2000, function ($timer_id) {
    echo "tick-2000ms\n";
});

//3000ms后执行此函数 仅执行一次
swoole_timer_after(3000, function () {
    echo "after 3000ms.\n";
});

以上代码可实现简易定时器