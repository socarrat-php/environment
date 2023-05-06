<?php

use PHPUnit\Framework\TestCase;
use Socarrat\Environment\EnvironmentManager;

final class EnvironmentManagerTest extends TestCase {
	public function testEscapeValue() {
		$em = new EnvironmentManager();

		$method = (new ReflectionClass($em))->getMethod("escapeValue");
		$method->setAccessible(true);

		$this->assertEquals("", $method->invoke($em, ""), "Empty string is untouched");
		$this->assertEquals("test", $method->invoke($em, "test"), "Normal string is untouched");

		$this->assertEquals("\n", $method->invoke($em, "\\n"), "Newline is escaped");
		$this->assertEquals("\t", $method->invoke($em, "\\t"), "Tab is escaped");
		$this->assertEquals("\"", $method->invoke($em, "\\\""), "Double quote is escaped");
		$this->assertEquals("\\", $method->invoke($em, "\\\\"), "Backslash is escaped");

		$this->assertEquals("\ttest\t", $method->invoke($em, "\\ttest\\t"), "Multiple tabs are escaped");

		$this->assertEquals(
			"Escape \t tabs \t, \n newlines \n, and \"quotes\" with a \\backslash\\",
			$method->invoke($em, "Escape \\t tabs \\t, \\n newlines \\n, and \\\"quotes\\\" with a \\\\backslash\\\\"),
			"Special characters are escaped"
		);
	}

	public function testGetParsedEnvEmpty() {
		$em = new EnvironmentManager();
		$env = $em::getParsedEnv();
		$this->assertEquals(0, sizeof($env));
	}

	public function testParseFS() {
		EnvironmentManager::parseFS(__DIR__, false);
		$env = EnvironmentManager::getParsedEnv();

		$expectedEnv = array(
			"FROM_ENV_LOCAL" => "This comes from .env.local",
			"OVERRIDE" => "This will be overridden by .env.local",
			"ESCAPED_VALUES" => "Escape \t tabs \t, \n newlines \n, and \"quotes\" with a \\backslash\\",
			"MULTILINE" => "Test
on multiple

lines………",
			"FROM_ENV" => "This comes from .env",
			"INTEGER" => "440",
			"DOUBLE" => "3.14",
		);

		$this->assertEquals(
			$expectedEnv,
			$env,
			".env files are parsed correctly and file hierachy is honoured"
		);
	}

	public function testParseString() {
		$string = '
			FROM_STR="This comes out of a string"
			INTEGER=440
			DOUBLE=3.14
		';

		EnvironmentManager::parseString($string, true);
		$env = EnvironmentManager::getParsedEnv();

		$this->assertEquals("This comes out of a string", $env["FROM_STR"]);
		$this->assertEquals("440", $env["INTEGER"]);
		$this->assertEquals("3.14", $env["DOUBLE"]);

		$this->assertEquals("This comes out of a string", getenv("FROM_STR"));
		$this->assertEquals("440", getenv("INTEGER"));
		$this->assertEquals("3.14", getenv("DOUBLE"));
	}
}
