<?php
namespace LiteTest;

use LiteTest;

use Colors\Color;

class CliRunner extends TestRunner
{
	/**
	 * Runs a suite of tests.
	 * @return void
	 */
	public function run() : void
	{
		$this->run();
		$formatter = new Formatter\Plaintext();
		$formatter->output_results($this->get_results());
	}
}
