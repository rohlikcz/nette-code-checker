<?php

/**
 * @testCase
 */

namespace DamejidloTests;

use CodeCheckers\LineLengthChecker;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';



class FakeChecker
{

	public $warning = [];
	public $error = [];



	public function warning($message)
	{
		$this->warning[] = $message;
	}



	public function error($message)
	{
		$this->error[] = $message;
	}



	public function is($file)
	{
		return TRUE;
	}
}



class LineLengthCheckerTest extends Tester\TestCase
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
	 * @dataProvider getDataForTestTooLengthFiles
	 */
	public function testTooLengthFiles($s, $countOfMessages)
	{
		$checker = LineLengthChecker::createLineLengthChecker(30);

		$checker($this->fakeChecker, $s);
		Assert::count($countOfMessages, $this->fakeChecker->warning);
		foreach ($this->fakeChecker->warning as $message) {
			Assert::match('%A%have%A%characters', $message);
		}
	}



	public function getDataForTestTooLengthFiles()
	{
		$s = str_repeat('a', 32);

		return [
			[$s, 1],
			["\n\n\n\n$s\n$s", 2],
			["foo\nbar\nfoo\n$s\n$s\n$s", 3],
			["__construct(foo$s,$s", 1],
		];
	}



	/**
	 * @dataProvider getDataForTestPass
	 */
	public function testPass($s)
	{
		$checker = LineLengthChecker::createLineLengthChecker(30);

		$checker($this->fakeChecker, $s);
		Assert::count(0, $this->fakeChecker->warning);
	}



	public function getDataForTestPass()
	{
		$s = str_repeat('a', 15);

		return [
			[$s],
			["\n\n\n\n$s\n$s"],
			["foo\nbar\nfoo\n$s\n$s\n$s"],
		];
	}



	public function testTabsToSpaces()
	{
		$checker = LineLengthChecker::createLineLengthChecker(10);

		$checker($this->fakeChecker, "\t\t");
		Assert::count(0, $this->fakeChecker->warning);

		$checker($this->fakeChecker, "\t\t\t"); //3 tabs = 12 characters
		Assert::count(1, $this->fakeChecker->warning);

		foreach ($this->fakeChecker->warning as $message) {
			Assert::match('%A%have%A%characters', $message);
		}
	}


	public function testErrorAndWarning()
	{
		$checker = LineLengthChecker::createLineLengthChecker(10, 20);
		$warningLine = str_repeat('a', 12);
		$errorLine = str_repeat('a', 22);

		Assert::count(0, $this->fakeChecker->warning);
		Assert::count(0, $this->fakeChecker->error);

		$checker($this->fakeChecker, $warningLine);
		Assert::count(1, $this->fakeChecker->warning);
		Assert::count(0, $this->fakeChecker->error);

		$checker($this->fakeChecker, $errorLine);
		Assert::count(1, $this->fakeChecker->warning);
		Assert::count(1, $this->fakeChecker->error);
	}



	public function testDisableWarning()
	{
		$checker = LineLengthChecker::createLineLengthChecker(NULL, 20);
		$warningLine = str_repeat('a', 12);
		$errorLine = str_repeat('a', 22);

		Assert::count(0, $this->fakeChecker->warning);
		Assert::count(0, $this->fakeChecker->error);

		$checker($this->fakeChecker, $warningLine);
		Assert::count(0, $this->fakeChecker->warning); //zero, disabled warning
		Assert::count(0, $this->fakeChecker->error);

		$checker($this->fakeChecker, $errorLine);
		Assert::count(0, $this->fakeChecker->warning);
		Assert::count(1, $this->fakeChecker->error);
	}

}



\run(new LineLengthCheckerTest());
