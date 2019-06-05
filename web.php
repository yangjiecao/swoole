<?php
require_once 'Test/Web.class.php';

$http = new swoole_http_server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        return $response->end();
    }
    list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));	
    var_dump($request->server,$request->get);
    $response->header("Content-Type", "text/html; charset=utf-8");
    list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));
    $data = (new $controller)->$action($request);
    $json = json_encode(['name'=>'roya','project'=>'swoole','controller'=>$controller,'action'=>$action,'data'=>$data]);
    go(function () {
        $swoole_mysql = new Swoole\Coroutine\MySQL();
        $swoole_mysql->connect([
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => 'cyj#123',
            'database' => 'test',
        ]);
        $res = $swoole_mysql->query('show tables');
        if($res === false) {
            echo 'Connect: Failed';
        }else{
            var_dump($res);
        }    
    });
    echo $json;
    $response->end($json);
    // $response->end("<h1>Hello Swoole.## #".rand(1000, 9999)."</h1>");	
});

$http->start();
