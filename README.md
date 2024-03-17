# Reference implementation of the CMS HealthCheck RFC using `\JsonSerializable` interface

## Introduction

This library provides an example reference implementation of the
[CMS HealthCheck RFC](https://github.com/cms-health-project/health-check-rfc)
using the `\JsonSerializable` interface to avoid the usage of a
dedicated formatter or builder as it is integrated into the classes
directly.

## Maintainer

This package has been created and is maintained by [Stefan Bürk <stefan@buerk.tech>](https://github.com/sbuerk).

## Installation

To use this library in your project or library, require it with:

```terminal
conmposer require "cms-health-project/serializable-reference-implementation"
```

## Usage

Build up the result structure:

```php
$time = \DateTime::createFromFormat('Y-m-d\TH:i:sP', '2024-03-19T01:23:45+00:00');
$check1 = new Check('package:check-one');
$check1->addCheckResults(
    new CheckResult(
        'check1-component1',
        'system',
        CheckResultStatus::Pass,
        $time,
    ),
);
$check2 = new Check('package:check-two');
$check2->addCheckResults(
    new CheckResult(
        'check2-component1',
        'system',
        CheckResultStatus::Fail,
        $time,
        'observed-value',
        'with-unit',
        'and-output'
    ),
);
$checks = new CheckCollection();
$checks->addCheck($check1, $check2);
$healthCheck = new HealthCheck(
    HealthCheckStatus::Fail,
    '1',
    'service-id',
    'description',
    $checks,
);
```

and than simply use `json_encode()` to create the json:

```php
$json = \json_encode($healthCheck, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
```

and you get the json, for example:

```json
{
    "status": "fail",
    "version": "1",
    "serviceId": "service-id",
    "description": "description",
    "checks": {
        "package:check-one": [
            {
                "componentId": "check1-component1",
                "componentType": "system",
                "status": "pass",
                "time": "2024-03-19T01:23:45+00:00"
            }
        ],
        "package:check-two": [
            {
                "componentId": "check2-component1",
                "componentType": "system",
                "status": "fail",
                "time": "2024-03-19T01:23:45+00:00",
                "output": "and-output",
                "observedValue": "observed-value",
                "observedUnit": "with-unit"
            }
        ]
    }
}
```

## Contribution

This package contains for now only a cgl check based on `php-cs-fixer`.

```terminal
composer install
composer cgl:fix
```