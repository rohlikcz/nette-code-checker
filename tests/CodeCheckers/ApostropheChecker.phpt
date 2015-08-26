<?php
/**
 * @testCase
 */

namespace CodeCheckerTests;

use CodeCheckers\ApostropheChecker;
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



class ApostropheCheckerTest extends TestCase
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
	 * @dataProvider getDataForTestChecker
	 */
	public function testChecker($input, $output, $countOfFixMessages)
	{
		$annotationChecker = ApostropheChecker::createChecker();
		Assert::same($output, $annotationChecker($this->fakeChecker, $input));
		Assert::count($countOfFixMessages, $this->fakeChecker->fixMessages);
	}



	public function getDataForTestChecker()
	{
		return [
			['<?php $a = "some";', '<?php $a = \'some\';', 1],

			['$a = "some " . "some two"', '$a = \'some \' . \'some two\'', 1],

			['->setAttribute("class", "btn");', '->setAttribute(\'class\', \'btn\');', 1],

			['"https://connection.keboola.com"', "'https://connection.keboola.com'", 1],
			//['$this->_log("Bucket {$result["id"]} created", ["options" => $options, "result" => $result]);',
			//'$this->_log(\'Bucket \' . $result[\'id\'] . \' created\', [\'options\' => $options, \'result\' => $result]);', 1],
			//['"bucketId" => $bucketId,', '\'bucketId\' => $bucketId,', 1],
			['"Šálek"', "'Šálek'", 1],
			['$to = $credit->orderRelated->restaurant->country->isCzech() ? "restaurace@damejidlo.cz" : "restauracie@dajmejedlo.sk";',
				'$to = $credit->orderRelated->restaurant->country->isCzech() ? \'restaurace@damejidlo.cz\' : \'restauracie@dajmejedlo.sk\';', 1],

			//['"timezone" => \'0\',', "'timezone' => '0',", 1],
			//['"data" => "@$dataFile"', '\'data\' => "@$dataFile"', 1],
			['"Stupava (Slovensko)"', '\'Stupava (Slovensko)\'', 1],
		];
	}



	/**
	 * @dataProvider getDataForTestCheckerNoChange
	 */
	public function testCheckerNoChange($input)
	{
		$annotationChecker = ApostropheChecker::createChecker();
		Assert::same($input, $annotationChecker($this->fakeChecker, $input));
		Assert::count(0, $this->fakeChecker->fixMessages);
	}



	public function getDataForTestCheckerNoChange()
	{
		return [
			['throw new AlreadyRevertedException("Objednávka \"{$orderId}\" už byla vrácena.");'],
			['if (!Strings::match($ic, "~" . self::FORMAT_REGEXP . "~ui")) {'],
			['WHEN `order_1_total_price` < :bronzeAmount OR `order_1_total_price` IS NULL THEN "none"'],
			[' * @User(role="customer_support")'],
			['"foo\n\tlol"'],
			['"Key $key of given row is missing"'],
			['\'<div class="more"><a href="%s">zobrazit všechny produkty <span>(%u)</span></a></div>\''],
			['<?php $a = "some \n"'],
			['[" ", "\r", "\n", "\t"]']
		];
	}

}



\run(new ApostropheCheckerTest());
