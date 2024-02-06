# Hailo Redirections

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pardalsalcap/hailo-redirections.svg?style=flat-square)](https://packagist.org/packages/pardalsalcap/hailo-redirections)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pardalsalcap/hailo-redirections/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pardalsalcap/hailo-redirections/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/pardalsalcap/hailo-redirections/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/pardalsalcap/hailo-redirections/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pardalsalcap/hailo-redirections.svg?style=flat-square)](https://packagist.org/packages/pardalsalcap/hailo-redirections)

This is an add-on for Hailo that allows you to log 404 errors and create redirections for them.

## Installation

You can install the package via composer:

```bash
composer require pardalsalcap/hailo-redirections
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="hailo-redirections-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="hailo-redirections-config"
```

This is the contents of the published config file:

```php
return [
    '404' => true,
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="hailo-redirections-views"
```

## Usage

Add the render method into the `app/Exceptions/Handler.php` file.

```php
public function render($request, Throwable $e) {
    if ($e instanceof NotFoundHttpException) {
        $http_status = $e->getStatusCode();
        $redirection_repository = new RedirectionRepository();
        $redirection = $redirection_repository->logError(request()->fullUrl(), $http_status);
        if (!empty($redirection->fix)) {
            return redirect($redirection->fix, $redirection->http_status);
        }
    }
    return parent::render($request, $e);
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [pardalsalcap](https://github.com/pardalsalcap)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
