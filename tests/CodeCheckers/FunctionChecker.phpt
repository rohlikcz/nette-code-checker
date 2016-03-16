<?php

/**
 * @testCase
 */

namespace DamejidloTests;

use CodeCheckers\FunctionChecker;
use Nette;
use Tester;
use Tester\Assert;
use Tester\TestCase;

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



class FunctionCheckerTest extends TestCase
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
		$html5Checker = FunctionChecker::createChecker();
		$html5Checker($this->fakeChecker, $s);
		Assert::count(0, $this->fakeChecker->errors);
	}



	public function getDataForTestPass()
	{
		return [
			["class\n\t\t{\n\t\tpublic function foo()\n\t\t}"],
			['class{	private	function foo()}'],
			["interface Foo\n\t\t\tfunction foo()"],
			['class protected function foo_foo('],
			["class\nfunction format_trace_file(\$trace, \$offset)"],
		];
	}



	/**
	 * @dataProvider getDataForTestFail
	 */
	public function testFail($s)
	{
		$html5Checker = FunctionChecker::createChecker();
		$html5Checker($this->fakeChecker, $s);
		Assert::count(1, $this->fakeChecker->errors);
	}



	public function getDataForTestFail()
	{
		return [
			["class\n\t\tfunction foo()"],
			["class\n    function foo2()"],
			["class	 */\n\tfunction getSubscribedEvents()\n\t{\n\treturn foo;\n}"],
		];
	}



	public function testFunctions()
	{
		$html5Checker = FunctionChecker::createChecker();
		$html5Checker($this->fakeChecker, "
			if (!function_exists('newrelic_notice_error')) {
				function newrelic_notice_error()
				{
					bd(func_get_args());
				}
			}");
		Assert::count(0, $this->fakeChecker->errors);
	}

}



\run(new FunctionCheckerTest());
