<?php

return new Phalcon\Config([
    '/content' => [
        'handler' => 'ContentController',
        'routes' => [
            '/@GET'  => [
                'action' => 'index',
                'configs' => [
                    'cacheTime' => 200
                ]
            ],
            '/@POST' => [
                'action' => 'add',
                'fields' => [
                    ['field' => "corvo"    , 'name' => "Corvo"   , 'rules' => "PresenceOf"],
                    ['field' => "cabrito"  , 'name' => "Cabrito" , 'rules' => "PresenceOf|Numericality"]
                ],

            ],
            '/@PUT' => [
                'action' => 'add',
                'fields' => [
                    ['field' => "corvo"    , 'name' => "Corvo"   , 'rules' => "PresenceOf"],
                    ['field' => "cabrito"  , 'name' => "Cabrito" , 'rules' => "PresenceOf|Numericality"]
                ],
            ],
            '/corvo/{id}/cabrito' => [
                'action' => 'index',
                'fields' => [
                    ['field' => "corvo"    , 'name' => "Corvo"   , 'rules' => "PresenceOf"],
                    ['field' => "cabrito"  , 'name' => "Cabrito" , 'rules' => "PresenceOf|Numericality"]
                ]
            ],
        ]
    ]
]);
