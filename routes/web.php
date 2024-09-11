<?php

$router->get('/api-docs', 'Swagger\SwaggerController@docs');

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->post('/spreadsheet', 'User\UserController@spreadsheet');
    $router->get('/', 'User\UserController@all');
    $router->patch('/{id}/name', 'User\UserController@editName');
    $router->patch('/{id}/cpf', 'User\UserController@editCpf');
    $router->patch('/{id}/email', 'User\UserController@editEmail');
    $router->get('/spreadsheet', 'User\UserController@createSpreadsheet');
});
