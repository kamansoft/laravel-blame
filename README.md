# laravel-blame

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kamansoft/laravel-blame.svg?style=flat-square)](https://packagist.org/packages/kamansoft/laravel-blame)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kamansoft/laravel-blame/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kamansoft/laravel-blame/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kamansoft/laravel-blame/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kamansoft/laravel-blame/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kamansoft/laravel-blame.svg?style=flat-square)](https://packagist.org/packages/kamansoft/laravel-blame)

A laravel package that will ease the usage of created by and updated by fields, on your Eloquent's models, it provide an abstract class from where you can extend your models, or you can implement a trait on your models.  


## Installation

You can install the package via composer:

```bash
composer require kamansoft/laravel-blame
```


This package comes with an User model that implements 


## Usage



```php
$laravelBlame = new Kamansoft\LaravelBlame();
echo $laravelBlame->echoPhrase('Hello, Kamansoft!');
```

### Migrations
Remeber to if your are using postgresql, mysql or mariadb you might need to install Doctrine DBAL package on your project, you can do that like so:
```bash
composer require doctrine/dbal
```
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [lemys lopez](https://github.com/lemyskaman)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
