<?php
namespace LiteTest;

/**
* A call stack for for an assertion. Maybe truncated to only those
* frames that are inside the active test case.
*/
class AssertionFrame
{
	public $frame;
	/** @param mixed $frame */
	function __construct($frame)
	{
		$this->frame = $frame;
	}
}
