<?php

namespace CodeCheckers;

use Nette\Utils\Strings;



class FunctionChecker
{

	public static function createChecker()
	{
		return function ($checker, $s) {
			if (!$checker->is('php,phpt')) {
				return;
			}
			if (Strings::match($s, '~interface ~')) {
				return;
			}
			if (!Strings::match($s, '~^(abstract\s+)?class~m')) {
				return;
			}

			if ($matchAll = Strings::matchAll($s, '~^([\s])+function(.*)~m')) {
				foreach ($matchAll as $match) {
					$checker->error(sprintf('Missing visibility on line: %s', Strings::trim($match[0])));
				}
			}

		};
	}
}
