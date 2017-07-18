Laravel Irradiate
=================

## Features

* Bulk insert or update
* Buffered inserts
* Chunk results and use limit at the same time
* Retry starting database transaction when connection is lost

## Installation ##

For Laravel 5.4:
```
composer require bogdanghervan/irradiate:5.4.*
```

For Laravel 5.0 up to and including Laravel 5.3:
```
composer require bogdanghervan/irradiate:5.0.*
```

## Configuration ##

To start using the package within Laravel, add this to the list of providers in `config/app.php`:

```
'Irradiate\Database\DatabaseServiceProvider',
```

Additionally, all the models should inherit from `\Irradiate\Database\Eloquent\Model`. Ideally there should already be a generic base model at the application level that all concrete models extend from, so make that extend the base model within Irradiate.

You're good to go!

## Limitations

MySQL only.

## Contributing

Pull requests are welcome. All contributions should follow the PSR-2 coding standard.

## License

Irradiate is licensed under the [MIT License](https://github.com/bogdanghervan/irradiate/blob/master/LICENSE).
