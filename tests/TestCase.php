<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    const AUTH_TOKENS
        = [
            'cloud@dolphin.ru.com' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6MzA1NlwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNjExNDU3NCwiZXhwIjoxNjY3MjE4NTc0LCJuYmYiOjE2MzYxMTQ1NzQsImp0aSI6IjNaQXRGOXBlcTFFWUFoeGIiLCJzdWIiOiI4NGJhNzEzNC1hNzVkLTQyOGEtOTE5NS0wYmFmZGUyODk2NGEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.VYe9LScVkhRIxi2P8tx0znBaFJOUAtH_TmzeY3OOCzI'
        ];
}
