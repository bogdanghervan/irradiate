Laravel Irradiate
=================

Database wizardry on top of Laravel excellence. This package comes with some goodies to work with data in bulk in Laravel,
however it is incomplete since it only implements the MySQL and SQLite grammars ([contribute](#contributing)).

## Features

### Bulk insert or update

This is achieved using `INSERT ... ON DUPLICATE KEY UPDATE` for MySQL and `INSERT ... ON CONFLICT DO UPDATE` for SQLite. 

### Buffered inserts

A service is provided that collects data in memory and flushes it to database when a threshold is reached.

### Legacy features

* Chunk results and use limit at the same time
* (MySQL only) Retry starting database transaction when connection is lost (no longer necessary in newer Laravel versions)

## Installation ##

Simply run this from the root of your Laravel project:
```
composer require bogdanghervan/irradiate
```

## Compatibility ##

This package supports Laravel 6, 7, 8, 9, 10, 11, and 12.

## Configuration ##

To start using the package within Laravel, add this to the list of providers in `config/app.php`:

```
'Irradiate\Database\DatabaseServiceProvider',
```

Or, for Laravel 5.5 and above (including Laravel 12), the package will be auto-discovered.

Additionally, all the models should inherit from `\Irradiate\Database\Eloquent\Model`. Ideally there should already be a generic base model at the application level that all concrete models extend from, so make that extend the base model within Irradiate.

You're good to go!

## Limitations

MySQL and SQLite only.

## Contributing

Pull requests are welcome. All contributions should follow the PSR-2 coding standard.

## License

Irradiate is licensed under the [MIT License](https://github.com/bogdanghervan/irradiate/blob/master/LICENSE).
