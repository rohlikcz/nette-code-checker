<?php
/**
 * @testCase
 */

namespace CodeCheckerTests;

use CodeCheckers\NeonChecker;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';



class FakeChecker
{

	public $fixMessages = [];



	public function fix($message)
	{
		$this->fixMessages[] = $message;
	}



	public function is($file)
	{
		return TRUE;
	}
}



class NeonCheckerTest extends TestCase
{

	/**
	 * @var FakeChecker
	 */
	private $fakeChecker;

	/**
	 * @var array
	 */
	private $input = [
		'common:
	php:
		include_path: %libsDir%
		date.timezone: Europe/Prague
		auto_detect_line_endings: no
		# zlib.output_compression: yes
',
		'common:
	php:
		include_path: %libsDir%
		date.timezone: Europe/Prague
		auto_detect_line_endings: true
		# zlib.output_compression: true
',
	];

	/**
	 * @var array
	 */
	private $output = [
		'common:
	php:
		include_path: %libsDir%
		date.timezone: Europe/Prague
		auto_detect_line_endings: false
		# zlib.output_compression: true
',
		'common:
	php:
		include_path: %libsDir%
		date.timezone: Europe/Prague
		auto_detect_line_endings: true
		# zlib.output_compression: true
',
	];



	public function setup()
	{
		$this->fakeChecker = new FakeChecker();
	}



	/**
	 * @dataProvider getDataForTestBooleanValues
	 */
	public function testBooleanValues($input, $output, $countOfFixMessages)
	{
		$booleanValuesChecker = NeonChecker::createBooleanValuesChecker();
		Assert::same($output, $booleanValuesChecker($this->fakeChecker, $input));
		Assert::count($countOfFixMessages, $this->fakeChecker->fixMessages);
	}



	public function getDataForTestBooleanValues()
	{
		return [
			[$this->input[0], $this->output[0], 2],
			[$this->input[1], $this->output[1], 0],
		];
	}

}



\run(new NeonCheckerTest());
