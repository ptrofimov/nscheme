<?php
/**
 * @author Petr Trofimov
 */
require_once ( dirname( __FILE__ ) . '/../NScheme/NScheme.class.php' );
require_once ( 'include/TinyRedisClient.class.php' );
require_once ( 'include/MyScheme.class.php' );

class NSchemeTest extends PHPUnit_Framework_TestCase
{
	public function testValue()
	{
		$my = new MyScheme();
		$my->value = 1;
		$this->assertEquals( 1, $my->value );
	}
	
	public function testHash()
	{
		$my = new MyScheme();
		$my->hash[ 'key' ] = 1;
		$this->assertEquals( 1, $my->hash[ 'key' ] );
	}
	
	public function testStructure()
	{
		$my = new MyScheme();
		$my->value2[ 'sdf' ]->value21 = 1;
		$this->assertEquals( 1, $my->value2[ 'sdf' ]->value21 );
		$my->value2->value21 = 1;
		$this->assertEquals( 1, $my->value2->value21 );
	}
	
	public function testSet()
	{
		$my = new MyScheme();
		$my->set->add( 'value' );
		$this->assertEquals( 1, $my->set->exists( 'value' ) );
		$this->assertEquals( array( 'value' ), $my->set->get() );
	}
	
	public function testQueue()
	{
		$my = new MyScheme();
		$my->queue->push( 'value' );
		$this->assertEquals( 0, $my->queue->isEmpty() );
		$this->assertEquals( 'value', $my->queue->shift() );
	}
	
	public function testStack()
	{
		$my = new MyScheme();
		$my->stack->push( 'stack_value' );
		$this->assertEquals( 'stack_value', $my->stack->pop() );
	}
}