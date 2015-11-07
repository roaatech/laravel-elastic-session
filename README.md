# Laravel Elastic Session Driver
An elastic-search based session driver for Laravel 5.1

##How to use
 1. Require it via composer

    ```
    composer require itvisionsy/laravel-elastic-sessions
    ```
 2. Add it to the providers list in `config/app.php`:

    ```php
    'providers' => [
    //...
    ItvisionSy\LaravelElasticSessionDriver\ElasticSessionServiceProvider::class,
    //...
    ]
    ```
 3. Set the correct settings in `config/session.php`

    ```php
    "driver" => "elastic",
    "elastic" => [
        "url" => "http://localhost:9200/",
        "index" => "laravel-es-sessions",
        "type" => "session"
    ],
    ```
    Values shown above are the default values in case you did not configure.

###Index/Type mapping
Elastic will detect the mapping by default, however, it is recommended to set the mapping explicitly.

You can do so manually by applying this mapping to the index and type:

```json
{
    "index":"set_the_index",
    "type":"set_the_type",
    "body":{
        "properties":{
            "created":{"type":"date"},
            "updated":{"type":"date"},
            "data":{"type":"string","index":"no"}
        }
    }
}
```

Or simpler, the package can do it for you. You will need to tinker `./artisan tinker` and then set the mapping:

```php
\ItvisionSy\LaravelElasticSessionDriver\ElasticSessionStore::putMapping();
```

## Author
Muhannad Shelleh <muhannad.shelleh@live.com>

## License
This code is published under [MIT](LICENSE) license.