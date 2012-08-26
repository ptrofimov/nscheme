<?php
/**
 * @author Petr Trofimov
 */
require_once 'NScheme.test.php';
class MySuite extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		$suite = new MySuite();
		$suite->addTestSuite( 'NSchemeTest' );
		return $suite;
	}
	
	protected function setUp()
	{
		print "\nMySuite::setUp()";
	}
	
	protected function tearDown()
	{
		print "\nMySuite::tearDown()";
	}
}