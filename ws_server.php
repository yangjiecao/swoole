<?php
//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("0.0.0.0", 9502);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    var_dump($request->fd, $request->get, $request->server);
    $ws->push($request->fd, "hello, welcome\n");
    // swoole 计时器
    global $timerId;
	$timerId = swoole_timer_tick(2000, function ($timer_id) use ($request, $ws) {
	    $ws->push($request->fd, "server: ".date('Y-m-d h:i:s', time()));
	});  
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
	var_dump($frame);
    echo "Message: {$frame->data}\n";
    $ws->push($frame->fd, "server: {$frame->data}");
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
    global $timerId;
    swoole_timer_clear($timerId); 
});

$ws->start();