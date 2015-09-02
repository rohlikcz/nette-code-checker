<?php

/**
 * @testCase
 */

namespace DamejidloTests;

use CodeCheckers\Html5Checker;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';



class FakeChecker
{

	public $errors = [];



	public function error($message)
	{
		$this->errors[] = $message;
	}



	public function is($file)
	{
		return TRUE;
	}
}



class Html5CheckerTest extends Tester\TestCase
{

	/**
	 * @var FakeChecker
	 */
	private $fakeChecker;



	public function setup()
	{
		$this->fakeChecker = new FakeChecker();
	}



	/**
	 * @dataProvider getDataForTestPass
	 */
	public function testPass($s)
	{
		$html5Checker = Html5Checker::createHtml5CheckerChecker();
		$html5Checker($this->fakeChecker, $s);
		Assert::count(0, $this->fakeChecker->errors);
	}



	public function getDataForTestPass()
	{
		return [
			[NULL],
			['<p>lol<br></p>'],
			['<br>lol<br><br>'],
		];
	}



	/**
	 * @dataProvider getDataForTestFail
	 */
	public function testFail($s)
	{
		$html5Checker = Html5Checker::createHtml5CheckerChecker();
		$html5Checker($this->fakeChecker, $s);
		Assert::count(1, $this->fakeChecker->errors);
	}



	public function getDataForTestFail()
	{
		return [
			['lol<br><br/>'],
			['<p>lol<br/></p>'],
			['<br>lol<br><br />'],
			["<br \t/>"],
			["<br\n/>"],
		];
	}
}



\run(new Html5CheckerTest());
