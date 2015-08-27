<?php

namespace CodeCheckers;

use Nette\Object;
use Nette\Utils\Strings;



class LineLengthChecker extends Object
{

	public static function createLineLengthChecker($maxLineLength = 120)
	{
		return function ($checker, $s) use ($maxLineLength) {
			if (!$checker->is('php') && !$checker->is('phpt')) {
				return;
			}

			$i = 1;
			foreach (explode("\n", $s) as $line) {
				$line = str_replace("\t", str_repeat(' ', 4), $line);
				if (Strings::length($line) > $maxLineLength) {
					$checker->warning(sprintf('Line %s have %d characters', Strings::truncate(Strings::trim($line), 30), Strings::length($line)), $i);
				}
				$i++;
			}
		};
	}
}
