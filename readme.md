# Socarrat Environment

A super simple `.env` file parser that is compatible with `getenv()`.

## API

### `class Socarrat\Environment\EnvironmentManager`

#### `static public function getParsedEnv(): array`

Returns all parsed environment variables as an associative array.

#### `static public function parseFS(string $rootDir, bool $putenv = true)`

Parses `.env` files from the filesystem.

This method reads the files in the order specified in EnvironmentManager::$fileOrder. You can set this order using setFileOrder.

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

## Copyright

(c) 2023 Romein van Buren. Licensed under the MIT license.

For the full copyright and license information, please view the [`license.md`](./license.md) file that was distributed with this source code.
