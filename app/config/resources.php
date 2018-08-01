<?php

return new Phalcon\Config([
    '/content' => [
        'handler' => 'ContentController',
        'routes' => [
            '/{id}@GET'  => [
                'action' => 'index',
                'configs' => [
                    'cacheTime' => 200
                ]
            ],
            '/@POST' => [
                'action' => 'create',
                'fields' => [
                    ['field' => "title"        , 'name' => "Title"       , 'rules' => "PresenceOf"],
                    ['field' => "description"  , 'name' => "Description" , 'rules' => "PresenceOf"],
                    ['field' => "order"        , 'name' => "Order"       , 'rules' => "PresenceOf|Numericality"]
                ],

            ],
            '/@PUT' => [
                'action' => 'update',
                'fields' => [],
            ],
            '/{id}/list-tags' => [
                'action' => 'listTags',
                'fields' => [],
                'configs' => [
                    'cacheTime' => 200
                ]
            ],
        ]
    ]
]);
