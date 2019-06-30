<?php
namespace LiteTest;

use LiteTest;

use Colors\Color;

class CliRunner extends TestRunner
{
	
	public function run()
	{
		$this->run();
		$formatter = new Formatter\Plaintext();
		$formatter->output_results($this->get_results());
	}
}
