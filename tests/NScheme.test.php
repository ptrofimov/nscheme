<?php
/**
 * @author Petr Trofimov
 */
require_once 'Value.test.php';
require_once 'Stack.test.php';
require_once 'Queue.test.php';
require_once 'Set.test.php';
require_once 'Hash.test.php';

class NSchemeTest extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		$suite = new self();
		$suite->addTestSuite( 'ValueTest' );
		$suite->addTestSuite( 'StackTest' );
		$suite->addTestSuite( 'QueueTest' );
		$suite->addTestSuite( 'SetTest' );
		$suite->addTestSuite( 'HashTest' );
		return $suite;
	}
	
	protected function setUp()
	{
	}
	
	protected function tearDown()
	{
	}
}