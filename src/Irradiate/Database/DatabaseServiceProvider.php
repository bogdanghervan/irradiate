<?php

namespace Irradiate\Database;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Connection as BaseConnection;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        BaseConnection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            return new MySqlConnection($connection, $database, $prefix, $config);
        });
        BaseConnection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new SQLiteConnection($connection, $database, $prefix, $config);
        });
    }
}

