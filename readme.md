# Projeto Rest-Micro PhalconPHP
---

Implementação de camada Middleware para arquitetura Micro do PhalconPHP.

Desenvolvimento da biblioteca Foundry para definições de rota e validação.

> Projeto em desenvolvimento


## 1. Instalação do PhalconPHP
https://docs.phalconphp.com/en/3.3/installation

## 2. Definição de rota

```
return new Phalcon\Config([
    '/content' => [ // Recurso / Prefixo da url;
        'handler' => 'ContentController', // Controller que atenderá a rota
        'routes' => [  // Define os endpoints daquele recurso;
            // [Pattern da Rota]@[Metodo HTTP]
            '/{id}@GET'  => [    
                'action' => 'index',
                'configs' => [
                    'cacheTime' => 200
                ]
            ],

            '/@POST' => [
                'action' => 'create',
                'fields' => [ // Lista de campos e validações;
                    ['field' => "title"        , 'name' => "Title"       , 'rules' => "PresenceOf"],
                    ['field' => "description"  , 'name' => "Description" , 'rules' => "PresenceOf"],
                    ['field' => "order"        , 'name' => "Order"       , 'rules' => "PresenceOf|Numericality"]
                    // Class Validator do PhalconPHP
                ],

            ],

            '/@PUT' => [
                'action' => 'update',
                'fields' => [],
            ],

            // Mapeia todos os métodos HTTP
            '/{id}/list-tags' => [
                'action' => 'listTags',
                'fields' => [],
                'configs' => [
                    'cacheTime' => 200
                ]
            ],
        ]
    ],
    '/category' => [ ... ] // Outro recurso
]);
```
> Para outras validações de campos ver: https://docs.phalconphp.com/en/3.3/validation

> A implementar regras especiais de validação como between

> Ainda não implementado método de autorização

> O array de configs será usado para definir configurações extras da rota.  
