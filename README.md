Laraport Session [![Build Status](https://travis-ci.org/laraport/session.svg?branch=master)](https://travis-ci.org/laraport/session)
======
Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

Based on Laravel (L4)'s illuminate/session library.

> Requires PHP 5.4+

# Table of contents

- [Install](#install)
- [Usage](#usage)
- [Test](#test)
- [License](#license)

# Install
```shell
$ composer require laraport/session
```

# Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Laraport\Session;

$Session = new Session([
    // ...
]);

```

> Yo!

> Hey!

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

# License

Released under the [MIT License](http://opensource.org/licenses/MIT).
