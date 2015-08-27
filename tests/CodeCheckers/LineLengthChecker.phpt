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

	public $messages = [];



	public function warning($message)
	{
		$this->messages[] = $message;
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
		$annotationChecker = LineLengthChecker::createLineLengthChecker(30);
		$annotationChecker($this->fakeChecker, $s);
		Assert::count($countOfMessages, $this->fakeChecker->messages);
		foreach ($this->fakeChecker->messages as $message) {
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
		$annotationChecker = LineLengthChecker::createLineLengthChecker(30);
		$annotationChecker($this->fakeChecker, $s);
		Assert::count(0, $this->fakeChecker->messages);
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
		$annotationChecker = LineLengthChecker::createLineLengthChecker(10);
		$annotationChecker($this->fakeChecker, "\t\t");
		Assert::count(0, $this->fakeChecker->messages);

		$annotationChecker($this->fakeChecker, "\t\t\t"); //3 tabs = 12 characters
		Assert::count(1, $this->fakeChecker->messages);

		foreach ($this->fakeChecker->messages as $message) {
			Assert::match('%A%have%A%characters', $message);
		}
	}

}



\run(new LineLengthCheckerTest());
