<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Request::setTrustedProxies(['127.0.0.1']);

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', []);
})
->bind('homepage');

$app->match('/call', function (Request $request) use ($app) {
    $post = [];
    $params =['url', 'method', 'data', 'type'];
    foreach ($params as $param)
        $post[$param] = $request->get($param, null);

    $method = $post['method'] ? $post['method'] : 'GET';
    $type = $post['type'] ? $post['type'] : 'json';

    return Cors::resposta($post['url'], $method, $post['data'], $type);
})
->method('PUT|POST|GET')
->bind('cors');

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug'])
        return;

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = [
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    ];

    return new Response($app['twig']->resolveTemplate($templates)->render(['code' => $code]), $code);
});

class Cors
{
    static private function build($data = null)
    {
        if($data)
            $data = json_decode($data, true);

        return $data;
    }

    static public function resposta($url, $method, $data = null, $type = "json")
    {
        $header = [
            "Accept: application/{$type}",
            "Accept-Encoding: gzip, deflate, sdch",
            "Accept-language: en-US",
            "Cache-Control: no-cache",
            "User-Agent:Lagden.in/1.0"
        ];

        $opts = [
            'http' => [
                'method' => $method
            ]
        ];

        $sendData = static::build($data);
        if($sendData)
        {
            $data_url = http_build_query ($sendData);
            $data_len = strlen ($data_url);
            if($method == 'GET')
                $url = "{$url}?{$data_url}";
            else
            {
                $opts['http']['content'] = $data_url;
                array_push($header, "Content-type: application/x-www-form-urlencoded");
                array_push($header, "Content-Length: {$data_len}");
            }
        }

        $opts['http']['header'] = implode($header, "\r\n");

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context, -1, 40000);

        return $response;
    }
}
