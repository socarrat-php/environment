<?php

use PHPUnit\Framework\TestCase;
use Socarrat\Environment\EnvironmentManager;

final class InvalidEnvironmentTest extends TestCase {
	public function testInvalidFileParsing() {
		EnvironmentManager::parseFS(__DIR__, true);
		$env = EnvironmentManager::getParsedEnv();

		$key = "@this-is-invalid!";
		$expected = "\\\\ ' \" Try to do your best with it, parser! '";

		$this->assertEquals($expected, $env[$key]);
		$this->assertEquals($expected, getenv($key));
	}
}
