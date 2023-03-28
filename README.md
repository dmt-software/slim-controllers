# Slim Controllers

## Installation

```bash
composer require dmt-software/slim-controllers
```

## Usage

```php
use DMT\Slim\Controller\Routing\ControllerMapper;
use Slim\Interfaces\RouteCollectorProxyInterface;

/** @var ControllerMapper $mapper */
$mapper->mapController(Controller::class);

// or within a group
$app->group('/amin', function(RouteCollectorProxyInterface $group) use ($mapper) {
    $mapper->mapController(OtherController::class, $group);
});
```
