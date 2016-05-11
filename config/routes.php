<?php
use Cake\Routing\Router;

Router::plugin(
    'Api',
    ['path' => '/api'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
