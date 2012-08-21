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
		
		$my->value = 'value1';
		$this->assertSame( 'value1', $my->value );
		
		$my->value = 'value2';
		$this->assertSame( 'value2', $my->value );
	}
	
	public function testStack()
	{
		$my = new MyScheme();
		
		$my->stack->push( 'value1' );
		$my->stack->push( 'value2' );
		
		$this->assertSame( 'value2', $my->stack->pop() );
		$this->assertSame( 'value1', $my->stack->pop() );
		$this->assertSame( null, $my->stack->pop() );
	}
	
	public function testStackAltSyntax()
	{
		$my = new MyScheme();
		
		$my->stack[] = 'value1';
		$my->stack[] = 'value2';
		
		$values = array();
		foreach ( $my->stack as $value )
		{
			$values[] = $value;
			if ( $value == 'value1' )
			{
				$my->stack[] = 'value3';
			}
		}
		
		$this->assertSame( array( 'value2', 'value1', 'value3' ), $values );
	}
	
	public function testQueue()
	{
		$my = new MyScheme();
		
		$my->queue->push( 'value1' );
		$my->queue->push( 'value2' );
		
		$this->assertSame( false, $my->queue->isEmpty() );
		$this->assertSame( 'value1', $my->queue->shift() );
		$this->assertSame( 'value2', $my->queue->shift() );
		$this->assertSame( null, $my->queue->shift() );
		$this->assertSame( true, $my->queue->isEmpty() );
	}
	
	public function testSet()
	{
		$my = new MyScheme();
		$my->set->add( 'value' );
		$this->assertEquals( 1, $my->set->exists( 'value' ) );
		$this->assertEquals( array( 'value' ), $my->set->get() );
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
		$my->struct[ 'sdf' ]->value = 1;
		$this->assertEquals( 1, $my->struct[ 'sdf' ]->value );
		$my->struct->value = 1;
		$this->assertEquals( 1, $my->struct->value );
	}
}