<?php

namespace CodeCheckers;

use Nette\Object;
use Nette\Utils\Strings;



class EntityChecker extends Object
{

	public static function createAnnotationsChecker()
	{
		return function ($checker, $s) {
			if (!$checker->is('php')) {
				return;
			}

			if (Strings::contains($s, '@ORM\Entity') && !Strings::contains($s, '@ORM\Entity(')) {
				$checker->fix('Missing Entity`()`');
				$s = str_replace('@ORM\Entity', '@ORM\Entity()', $s);
			}

			if (Strings::contains($s, '@ORM\Table()')) {
				$checker->fix('Missing `name="table_name"`');
				$s = str_replace('@ORM\Table()', '@ORM\Table(name="")', $s);
			}

			if (Strings::contains($s, '@ORM\Table') && !Strings::contains($s, '@ORM\Table(')) {
				$checker->fix('Missing `name="table_name"`');
				$s = str_replace('@ORM\Table', '@ORM\Table(name="")', $s);
			}

			return $s;
		};
	}
}
