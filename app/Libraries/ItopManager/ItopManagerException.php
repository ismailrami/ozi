<?php

namespace App\Libraries\ItopManager;

use Exception;

class ItopManagerException extends Exception
{
	function __construct($msg, $code = 0, Exception $previous = null) {
		if (!preg_match('/^ItopManager :/', $msg)) {
			$msg = "ItopManager : $msg";
		}
		parent::__construct($msg, $code, $previous);
	}
}
