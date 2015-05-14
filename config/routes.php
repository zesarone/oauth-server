<?php
\Cake\Routing\Router::plugin('OAuthServer', ['path' => '/oauth'], function(\Cake\Routing\RouteBuilder $routes) {
    $routes->connect(
        '/',
        [
            'controller' => 'OAuth',
            'action' => 'oauth'
        ]
    );
    $routes->connect(
        '/authorize',
        [
            'controller' => 'OAuth',
            'action' => 'authorize'
        ]
    );
    $routes->connect(
        '/access_token',
        [
            'controller' => 'OAuth',
            'action' => 'accessToken'
        ]
    );
    $routes->scope('/clients', ['controller' => 'Clients'], function (\Cake\Routing\RouteBuilder $routes) {
        $routes->connect('/', ['action' => 'index']);
        $routes->connect('/:action/*');
    });
});