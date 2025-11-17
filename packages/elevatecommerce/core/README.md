# ElevateCommerce Core

Core package for ElevateCommerce platform.

## Installation

This package is auto-discovered by Laravel. Simply require it in your `composer.json`:

```json
{
    "require": {
        "elevatecommerce/core": "*"
    },
    "repositories": [
        {
            "type": "path",
            "url": "./packages/elevatecommerce/core"
        }
    ]
}
```

Then run:

```bash
composer update elevatecommerce/core
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=core-config
```

## Views

Publish the views:

```bash
php artisan vendor:publish --tag=core-views
```

## Usage

Documentation coming soon.

## License

MIT
