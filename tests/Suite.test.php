<?php
/**
 * @author Petr Trofimov
 */
require_once ( dirname( __FILE__ ) . '/../NScheme/NScheme.class.php' );
require_once ( 'include/TinyRedisClient.class.php' );
require_once ( 'include/MyScheme.class.php' );
require_once 'Value.test.php';

class MySuite extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		$suite = new MySuite();
		$suite->addTestSuite( 'ValueTest' );
		return $suite;
	}
	
	protected function setUp()
	{
		//print "\nMySuite::setUp()";
	}
	
	protected function tearDown()
	{
		//print "\nMySuite::tearDown()";
	}
}