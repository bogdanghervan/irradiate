# Laravel Irradiate #

### Installation ###

Add this to the "require" section of the project's `composer.json` file:
```
"bogdanghervan/irradiate": "dev-master"
```

Now run `composer update bogdanghervan/irradiate` to get the package.

### Configuration ###

To start using the package within Laravel, add this to the list of providers in `config/app.php`:

```
'Irradiate\Database\DatabaseServiceProvider',
```

Additionally, all the models should inherit from `\Irradiate\Database\Eloquent\Model`. Ideally there should already be a generic base model at the application level that all concrete models extend from, so make that extend the base model within Irradiate.

You're good to go!
