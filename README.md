Laraport Session [![Build Status](https://travis-ci.org/laraport/session.svg?branch=master)](https://travis-ci.org/laraport/session)
======
This php library is an unofficial port of [laravel](http://laravel.com/) (L4) sessions. See [illuminate/session](https://github.com/illuminate/session/tree/4.2) for more details. The reason for this port is to consume it outside of laravel in a standalone project or another framework, where you find it tediuos to import the whole laravel framework.

This port currently supports the following drivers:
- Array (Default)
- File
- Database
- PHP's [SessionHandlerInterface](http://php.net/manual/en/class.sessionhandlerinterface.php) implementation

> Requires PHP 5.4 or greater.

I have tried my best to load the minimum required libraries and have avoided laravel's container to ensure maximum portability.

# Table of contents

- [Install](#install)
- [Usage](#usage)
    - [Array driver](#array-driver)
    - [File driver](#file-driver)
    - [Database driver](#database-driver)
    - [Custom driver](#custom-driver)
- [Test](#test)
- [Similar projects](#similar-projects)
- [License](#license)

# Install

This package can be installed by requiring it via [composer](https://getcomposer.org).

```shell
$ composer require laraport/session
```

# Usage

If you have consumed sessions in laravel (which i am sure you have), its now a cinch to consume it in a standalone project as well.

#### Array driver

The laravel [configuration](https://github.com/laraport/session/blob/master/src/config.php) is loaded by default which is set to use the array driver.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$Session = new Laraport\Session();
$Session->start();
// ...
$Session->put('laraport', 'session');
// ... You know the rest, right?
```

#### File driver

To consume the file session driver, you may override the necessary configuration.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$Session = new Laraport\Session([
    'driver' => 'file',
    'files' => __DIR__ . '/path/to/session'
]);
$Session->start();
// ...
```

#### Database driver

For the database session driver, you will need to pass the database connection array and set the table.

> Any connection that [illuminate/database](https://github.com/illuminate/session/tree/4.2) understands, is supported.

> The table is set to `sessions` by default.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$Session = new Laraport\Session([
    'driver' => 'database',
    'table' => 'sessions',
    'connection' => [
        'driver'   => 'sqlite',
        'database' => $database,
        'prefix'   => ''
    ]
]);
$Session->start();
// ...
```

#### Custom driver

A custom implementation of php's [SessionHandlerInterface](http://php.net/manual/en/class.sessionhandlerinterface.php) is also supported. Simply set the instance of the implementation as the `driver` .

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$Session = new Laraport\Session([
    'driver' => new MyCustomSessionHandler(/*...*/)
]);
$Session->start();
// ...
```

# Test
> *First make sure you are in the project source directory.*

Do a composer install.
```shell
$ composer install
```
Run the tests.
```shell
$ vendor/bin/phpunit
```
or
```shell
$ composer test
```

# Similar projects

- [Torch](https://github.com/mattstauffer/Torch/tree/4.2) - Supports file driver.
- [phpgearbox/session](https://github.com/phpgearbox/session) - Supports database driver.

> The above packages were the source of inspiration for this port.

# License

Released under the [MIT License](http://opensource.org/licenses/MIT).
