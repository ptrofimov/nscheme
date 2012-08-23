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
		
		$retval = $my->stack->clear();
		
		$this->assertSame( $my->stack, $retval );
		$this->assertSame( true, $my->stack->isEmpty() );
		$this->assertSame( 0, $my->stack->getCount() );
		$this->assertSame( null, $my->stack->pop() );
		$this->assertSame( null, $my->stack->peek() );
		
		$retval = $my->stack->push( 'value1' );
		
		$this->assertSame( $my->stack, $retval );
		$this->assertSame( false, $my->stack->isEmpty() );
		$this->assertSame( 1, $my->stack->getCount() );
		$this->assertSame( 'value1', $my->stack->peek() );
		
		$retval = $my->stack->push( 'value2' );
		
		$this->assertSame( $my->stack, $retval );
		$this->assertSame( false, $my->stack->isEmpty() );
		$this->assertSame( 2, $my->stack->getCount() );
		$this->assertSame( 'value2', $my->stack->peek() );
		
		$retval = $my->stack->pop();
		
		$this->assertSame( 'value2', $retval );
		$this->assertSame( false, $my->stack->isEmpty() );
		$this->assertSame( 1, $my->stack->getCount() );
		$this->assertSame( 'value1', $my->stack->peek() );
		
		$retval = $my->stack->pop();
		
		$this->assertSame( 'value1', $retval );
		$this->assertSame( true, $my->stack->isEmpty() );
		$this->assertSame( 0, $my->stack->getCount() );
		$this->assertSame( null, $my->stack->peek() );
		
		$retval = $my->stack->pop();
		
		$this->assertSame( null, $retval );
		$this->assertSame( true, $my->stack->isEmpty() );
		$this->assertSame( 0, $my->stack->getCount() );
		$this->assertSame( null, $my->stack->peek() );
	}
	
	public function testStackAltSyntax()
	{
		$my = new MyScheme();
		
		$my->stack->clear();
		
		$this->assertSame( 0, count( $my->stack ) );
		
		$my->stack[] = 'value1';
		$my->stack[] = 'value2';
		
		$this->assertSame( 2, count( $my->stack ) );
		
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
		$this->assertSame( 0, count( $my->stack ) );
	}
	
	public function testQueue()
	{
		$my = new MyScheme();
		
		$retval = $my->queue->clear();
		
		$this->assertSame( $my->queue, $retval );
		$this->assertSame( true, $my->queue->isEmpty() );
		$this->assertSame( 0, $my->queue->getCount() );
		$this->assertSame( null, $my->queue->shift() );
		$this->assertSame( null, $my->queue->peek() );
		
		$retval = $my->queue->push( 'value1' );
		
		$this->assertSame( $my->queue, $retval );
		$this->assertSame( false, $my->queue->isEmpty() );
		$this->assertSame( 1, $my->queue->getCount() );
		$this->assertSame( 'value1', $my->queue->peek() );
		
		$retval = $my->queue->push( 'value2' );
		
		$this->assertSame( $my->queue, $retval );
		$this->assertSame( false, $my->queue->isEmpty() );
		$this->assertSame( 2, $my->queue->getCount() );
		$this->assertSame( 'value1', $my->queue->peek() );
		
		$retval = $my->queue->shift();
		
		$this->assertSame( 'value1', $retval );
		$this->assertSame( false, $my->queue->isEmpty() );
		$this->assertSame( 1, $my->queue->getCount() );
		$this->assertSame( 'value2', $my->queue->peek() );
		
		$retval = $my->queue->shift();
		
		$this->assertSame( 'value2', $retval );
		$this->assertSame( true, $my->queue->isEmpty() );
		$this->assertSame( 0, $my->queue->getCount() );
		$this->assertSame( null, $my->queue->peek() );
		
		$retval = $my->queue->shift();
		
		$this->assertSame( null, $retval );
		$this->assertSame( true, $my->queue->isEmpty() );
		$this->assertSame( 0, $my->queue->getCount() );
		$this->assertSame( null, $my->queue->peek() );
	}
	
	public function testQueueAltSyntax()
	{
		$my = new MyScheme();
		
		$my->queue->clear();
		
		$this->assertSame( 0, count( $my->queue ) );
		
		$my->queue[] = 'value1';
		$my->queue[] = 'value2';
		
		$this->assertSame( 2, count( $my->queue ) );
		
		$values = array();
		foreach ( $my->queue as $value )
		{
			$values[] = $value;
			if ( $value == 'value1' )
			{
				$my->queue[] = 'value3';
			}
		}
		
		$this->assertSame( array( 'value1', 'value2', 'value3' ), $values );
		$this->assertSame( 0, count( $my->queue ) );
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