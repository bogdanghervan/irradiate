<?php

namespace Irradiate\Database;

use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('db.connection.mysql', '\Irradiate\Database\MySqlConnection');
    }
}

