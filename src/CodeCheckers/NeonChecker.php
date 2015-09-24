<?php

namespace CodeCheckers;

use Nette\Object;



class NeonChecker extends Object
{

	public static function createBooleanValuesChecker()
	{
		return function ($checker, $s) {
			if (!$checker->is('neon')) {
				return;
			}

			$lines = explode("\n", $s);
			foreach ($lines as &$line) {
				if (preg_match('~^(.*):( )?(yes|on|no|off)$~i', $line)) {
					$message = sprintf('Boolean values should be true/false: %s', $line);
					$checker->fix($message);
					$line = preg_replace('~:( )?(yes|on)$~i', ': true', $line);
					$line = preg_replace('~:( )?(no|off)$~i', ': false', $line);
				}
			}

			return implode("\n", $lines);
		};
	}
}
