<?php

namespace Socarrat\Environment;

class EnvironmentManager {
	/**
	 * The order in which `.env` files are loaded.
	 *
	 * Lower index means higher importance.
	 */
	static protected $fileOrder = [
		".env.local",
		".env.shared",
		".env",
	];

	/**
	 * The parsed environment as an associative array.
	 *
	 * Both keys and values are strings.
	 */
	static protected $environment = array();

	/**
	 * Returns all parsed environment variables as an associative array.
	 */
	static public function getParsedEnv(): array {
		return static::$environment;
	}

	/**
	 * Sets the order in which `.env` files are loaded.
	 *
	 * Lower index means higher importance.
	 */
	static public function setFileOrder(array $order): void {
		static::$fileOrder = $order;
	}

	/**
	 * Escapes a raw value as encountered in a `.env` file.
	 */
	static protected function escapeValue(string $val): string {
		$val = str_replace("\\n", "\n", $val);
		$val = str_replace("\\t", "\t", $val);
		$val = str_replace("\\\"", "\"", $val);
		$val = str_replace("\\\\", "\\", $val);
		return $val;
	}

	/**
	 * Saves the provided environment variables.
	 */
	static protected function saveEnv(array $env, bool $putenv): void {
		// Set provided environment variables, and make sure not to completely
		// override the entire array. Instead merge the two.
		foreach ($env as $key => $value) {
			static::$environment[$key] = $value;
		}

		// Write to environment if desired.
		if ($putenv) {
			foreach (static::$environment as $key => $value) {
				putenv("$key=$value");
			}
		}
	}

	/**
	 * This is the base parse function. It parses `.env` files line by line.
	 */
	static protected function parseLineByLine(array $lines): array {
		$parsedEnv = array();

		// Get index of the last line
		$lastLineIndex = count($lines) - 1;

		// Trim file
		while (isset($lines[0]) and (trim($lines[0]) == "")) {
			$lines = array_slice($lines, 1);
		}
		while (isset($lines[$lastLineIndex]) and (trim($lines[$lastLineIndex]) == "")) {
			$lines = array_slice($lines, --$lastLineIndex);
		}

		// Continue if file is empty
		if ($lastLineIndex === 0) {
			return array();
		}

		$keyFromPrevLine = false;
		$valueOnPrevLine = false;

		// Parse each line separately
		foreach ($lines as $idx => $line) {

			// Trim whitespace from right side. It's unwanted to trim the
			// left side if the value continues on the next line and starts
			// with whitespace.
			$line = rtrim($line);

			// New key/value pair.
			if ($valueOnPrevLine === false) {

				// Now it's safe to trim whitespace from left side.
				$line = ltrim($line);

				// Continue if the line is empty.
				if ($line === "") {
					continue;
				}

				// Get index of = character
				$equalIndex = strpos($line, "=");

				// Continue if line does not contain = or a key
				if (($equalIndex === false) or ($equalIndex < 1)) {
					continue;
				}

				// Extract key and value
				$key = rtrim(substr($line, 0, $equalIndex));
				$val = ltrim(substr($line, $equalIndex + 1));

				// Value starts and ends with " character: trim them off.
				if (($val[0] === "\"") and (substr($val, -1) === "\"")) {
					$val = substr($val, 1, -1);
				}

				// Value starts with " but does not end with it: value
				// continues on the next line
				else if (($val[0] === "\"") and ($idx !== $lastLineIndex)) {
					$valueOnPrevLine = substr($val, 1);
					$keyFromPrevLine = $key;
					continue;
				}

				// Replace escaped characters
				$val = static::escapeValue($val);

				// Set key and value
				$parsedEnv[$key] = $val;
			}

			// Value continues on the next line.
			else {

				// As this is the next line, it's needed to register the
				// line break.
				$valueOnPrevLine .= "\n";

				// This is the ultimate line if it ends in a " character.
				// Whitespace is already trimmed off the right side of the
				// line, so it won't be in the way for this check.
				if (substr($line, -1) === "\"") {

					// Get key and value.
					$key = $keyFromPrevLine;
					$val = $valueOnPrevLine . static::escapeValue(
						// Trim trailing " character from the line.
						substr($line, 0, -1)
					);

					// Parse next line normally
					$valueOnPrevLine = false;
					$keyFromPrevLine = false;

					// Set key and value
					$parsedEnv[$key] = $val;
				}

				// Not the last line.
				else {

					// Append the line contents to the value.
					$valueOnPrevLine .= static::escapeValue($line);
				}
			}
		}

		return $parsedEnv;
	}

	/**
	 * Parses `.env` files from the filesystem.
	 *
	 * This method reads the files in the order specified in EnvironmentManager::$fileOrder. You can set this order using EnvironmentManager::setFileOrder.
	 *
	 * @param $rootDir The root directory which contains your `.env` file/s.
	 * @param $putenv Whether to register the values with PHP's environment, so that they can be retrieved using `getenv()`.
	 */
	static public function parseFS(string $rootDir, bool $putenv = true) {
		$env = array();

		// Parse each file.
		foreach (static::$fileOrder as $fname) {
			// Read file line by line.
			$path = preg_replace('#/+#', '/', $rootDir."/".$fname);
			$lines = @file($path);

			// Continue if file is not found.
			if ($lines === false) {
				continue;
			}

			$parsedEnv = static::parseLineByLine($lines);
			foreach ($parsedEnv as $key => $value) {
				$env[$key] = $value;
			}
		}

		// Almost done...
		static::saveEnv($env, $putenv);
	}

	/**
	 * Parses a single `.env` file passed as a string.
	 *
	 * @param $envFile The string to parse.
	 * @param $putenv Whether to register the values with PHP's environment, so that they can be retrieved using `getenv()`.
	 */
	static public function parseString(string $envFile, bool $putenv = true) {
		// Parse the passed string...
		$env = static::parseLineByLine(
			// And do that line by line.
			explode("\n", $envFile)
		);

		// Save it.
		static::saveEnv($env, $putenv);
	}
}
