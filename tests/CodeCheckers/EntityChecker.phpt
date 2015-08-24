<?php
/**
 * @testCase
 */

namespace CodeCheckerTests;

use CodeCheckers\EntityChecker;
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



class EntityCheckerTest extends TestCase
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
	 * @dataProvider getDataForTestEntity
	 */
	public function testEntity($input, $output, $countOfFixMessages)
	{
		$annotationChecker = EntityChecker::createAnnotationsChecker();
		Assert::same($output, $annotationChecker($this->fakeChecker, $input));
		Assert::count($countOfFixMessages, $this->fakeChecker->fixMessages);
	}



	public function getDataForTestEntity()
	{
		return [
			['@ORM\Entity', '@ORM\Entity()', 1],
			['@ORM\Entity()', '@ORM\Entity()', 0],
		];
	}



	/**
	 * @dataProvider getDataForTestTableName
	 */
	public function testTableName($input, $output, $countOfFixMessages)
	{
		$annotationChecker = EntityChecker::createAnnotationsChecker();
		Assert::same($output, $annotationChecker($this->fakeChecker, $input));
		Assert::count($countOfFixMessages, $this->fakeChecker->fixMessages);
	}



	public function getDataForTestTableName()
	{
		return [
			['@ORM\Table()', '@ORM\Table(name="")', 1],
			['@ORM\Table', '@ORM\Table(name="")', 1],
			['@ORM\Table(name="table_name")', '@ORM\Table(name="table_name")', 0],
		];
	}
}



\run(new EntityCheckerTest());
