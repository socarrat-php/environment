# Socarrat\Environment

A super simple `.env` file parser that is compatible with [`getenv()`](https://www.php.net/manual/en/function.getenv.php).

[![Test](https://github.com/socarrat-php/environment/actions/workflows/test.yml/badge.svg?event=push)](https://github.com/socarrat-php/environment/actions/workflows/test.yml)

## Features

- [x] Parses `.env` files and does it well
- [x] Parses `.env` files from the filesystem and from string input
- [x] [Special characters](#special-characters)
- [x] [Compatible with `getenv()`](#how-about-getenv): no need to adapt your existing applications
- [x] Fault-tolerant
- [x] [Well-tested](#testing)
- [x] [Multi-line values](#multi-line-values)
- [ ] Support for comments (WIP)
- [ ] Invalid file warnings (WIP)

## Installation

Download this [package](https://packagist.org/packages/socarrat/environment) on Packagist:

```sh
composer require socarrat/environment
```

## Usage

The [`EnvironmentManager`](#class-socarratenvironmentenvironmentmanager) is responsible for parsing `.env` files [on the filesystem](#static-public-function-parsefsstring-rootdir-bool-putenv--true) (or, if you like, [as strings](#static-public-function-parsestringstring-envfile-bool-putenv--true)). It does so on request: you need to call one (or more) of the parsing methods (see [API](#api)) to load your environment variables.

### How about `getenv()`?

One of the parameters of the parse functions is `bool $putenv`. If you set this to true (default), [`putenv()`](https://www.php.net/manual/en/function.putenv.php) will be called for every parsed variable. If you already use `getenv()`, there's no need to adapt your program: you only need to call a parse function once at the start.

### Examples

See the [`examples/` directory](./examples/) for some usage examples.

## `.env` file format

### The basics

A `.env` file contains key-value pairs of environment-specific settings. Each line contains a key-value pair. Keys [should](https://www.rfc-editor.org/rfc/rfc2119#section-3) be capitalised, and should only contain A-Z characters and underscores. Thereafter comes an `=` sign, and then the value. See the following example:

```
DB_ENGINE=mysql
DB_NAME=example
```

### Quoted values

To avoid confusion, you could enclose the value in _double_ quotes `"`.

```
DB_PASSWORD="Welkom2018"
```

Note that single quotes `'` are NOT stripped from the value! For example, the value of `DB_TABLE='users'` is `'users'` and not `users`.

### Multi-line values

Multi-line values are supported, provided that the value is enclosed in double quotes.

```
TEXT="Calculate density using:

  ρ = m/V

where ρ is the density,
      m is the mass,
  and V is the volume.
"
```

### Special characters

You can use the following special characters in your values:

| Name               | Notation |
|--------------------|:--------:|
| Backslash (`\`)    |   `\\`   |
| Double quote (`"`) |   `\"`   |
| Newline            |   `\n`   |
| Tab                |   `\t`   |

Example:

```
ESCAPED_VALUES="Escape \t tabs \t, \n newlines \n, and \"quotes\" with a \\backslash\\"
```

### Whitespace handling

Whitespace is handled thus:

* Leading whitespace before keys is ignored.

* Trailing whitespace after values is always trimmed, except when the value including whitespace is enclosed by double quotes.

* Leading whitespace on a newline that is part of the value is honoured.

* Trailing whitespace on a newline that is part of the value is always ignored, except when the line is the last line that is part of the value and the whitespace comes before the closing double quote.

## API

### `class Socarrat\Environment\EnvironmentManager`

#### `static public function getParsedEnv(): array`

Returns all parsed environment variables as an associative array.

#### `static public function parseFS(string $rootDir, bool $putenv = true)`

Parses `.env` files from the filesystem.

This method reads the files in the order specified in EnvironmentManager::$fileOrder. You can set this order using [setFileOrder](#static-public-function-setfileorderarray-order-void).

| Parameter name | Type      | Default value | Description                                                                                            |
|----------------|-----------|---------------|--------------------------------------------------------------------------------------------------------|
| `$rootDir`     | `string`  | -             | The root directory which contains your `.env` file/s.                                                  |
| `$putenv`      | `bool`    | `true`        | Whether to register the values with PHP's environment, so that they can be retrieved using `getenv()`. |

#### `static public function parseString(string $envFile, bool $putenv = true)`

Parses a single `.env` file passed as a string.

| Parameter name | Type      | Default value | Description                                                                                            |
|----------------|-----------|---------------|--------------------------------------------------------------------------------------------------------|
| `$envFile`     | `string`  | -             | The string to parse                                                                                    |
| `$putenv`      | `bool`    | `true`        | Whether to register the values with PHP's environment, so that they can be retrieved using `getenv()`. |

#### `static public function setFileOrder(array $order): void`

Sets the order (passed as the `$order` parameter) in which `.env` files are loaded. Lower index means higher importance.

The default file order is:

```php
[
	".env.local",
	".env.shared",
	".env",
]
```

## Testing

This library is unit-tested by [PHPunit](https://phpunit.de/) using the tests in the [`tests/` directory](./tests/). You can run the test suite on your machine (`composer run-script test`). Test results for each commit (run on Ubuntu 22.04) are available [on GitHub](https://github.com/socarrat-php/environment/actions/workflows/test.yml).

## Copyright

(c) 2023 Romein van Buren. Licensed under the MIT license.

For the full copyright and license information, please view the [`license.md`](./license.md) file that was distributed with this source code.
