<?php

return new Phalcon\Config(
    [
        '/content' =>
        [
            'GET' =>
            [
                'fields' => [
                    ['field' => "corvo"    , 'name' => "Corvo"   , 'rules' => "PresenceOf"],
                    ['field' => "cabrito"  , 'name' => "Cabrito" , 'rules' => "PresenceOf|Numericality"]

                ],
                'configs' =>
                [
                    'deleteUseless'   => true,
                    'login'           => false,                                        
                    'cacheTime'       => 20
                ]
            ]
        ]
    ]
);
