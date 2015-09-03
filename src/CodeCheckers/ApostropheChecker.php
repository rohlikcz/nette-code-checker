<?php

namespace CodeCheckers;

use Nette\Object;
use Nette\Utils\Strings;



class ApostropheChecker extends Object
{

	public static function createChecker()
	{
		return function ($checker, $s) {
			if (!$checker->is('php') && !$checker->is('phpt')) {
				return;
			}

			$s = implode("\n", array_map(function ($line) use ($checker) {
				$line = rtrim($line);
				if (Strings::contains($line, "'")
					|| Strings::contains($line, '*')
					|| Strings::contains($line, "'")
					|| Strings::contains($line, "\n")
					|| Strings::contains($line, '`') //may SQL query
					|| Strings::contains($line, '{$')
					|| Strings::contains($line, '~')
					|| Strings::contains($line, '\\')
				) {
					return $line;
				}

				$matches = Strings::matchAll($line, '#"([a-z0-9 \(\)\@\w\:\/\-\_\#\.]+)"(.*)#imu');
				$match = isset($matches[0][0]) ? $matches[0][0] : FALSE;
				if (!$match) {
					return $line;
				}
				$new = str_replace('"', "'", $match);
				$line = str_replace($match, $new, $line);
				$checker->fix(sprintf('%s changed to %s', $match, $new));

				return $line;
			}, explode("\n", $s)));

			return $s;
		};
	}
}
