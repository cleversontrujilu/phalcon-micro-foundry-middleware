<?php

return new Phalcon\Config(
    [
        '/content' =>
        [
            'Handler' => "ContentController",
            
            'routs' => [
                '/' => [
                    'GET' =>
                    [
                        'action' => 'index',
                        'fields' => [
                            ['field' => "corvo"    , 'name' => "Corvo"   , 'rules' => "PresenceOf"],
                            ['field' => "cabrito"  , 'name' => "Cabrito" , 'rules' => "PresenceOf|Numericality"]

                        ],
                        'configs' =>
                        [
                            'deleteUseless'   => true,
                            'login'           => false,
                            'cacheTime'       => 0
                        ]
                    ]
                ],

                '/corvo' => [
                    'GET' =>
                    [
                        'action' => 'index',
                        'fields' => [
                            ['field' => "corvo"    , 'name' => "Corvo"   , 'rules' => "PresenceOf"],
                            ['field' => "cabrito"  , 'name' => "Cabrito" , 'rules' => "PresenceOf|Numericality"]

                        ],
                        'configs' =>
                        [
                            'deleteUseless'   => true,
                            'login'           => false,
                            'cacheTime'       => 0
                        ]
                    ]
                ]
            ]
        ]

    ]
);
