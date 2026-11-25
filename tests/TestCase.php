<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();
        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.$connection.database");
        // Abort BEFORE RefreshDatabase can migrate any non-test database.
        if (! in_array($database, [':memory:', 'incubadora_test'], true)) {
            throw new \RuntimeException('Pruebas bloqueadas: la conexión debe apuntar a :memory: o incubadora_test.');
        }

        return $app;
    }
}
