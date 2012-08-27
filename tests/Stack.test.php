<?php
/**
 * @author Petr Trofimov
 */
require_once ( dirname( __FILE__ ) . '/../NScheme/NScheme.class.php' );
require_once ( 'include/TinyRedisClient.class.php' );
require_once ( 'include/MyScheme.class.php' );

class StackTest extends PHPUnit_Framework_TestCase
{
	public function dataProvider()
	{
		$scheme = new MyScheme();
		
		$data = array();
		$data[] = array( $scheme, 'stack' );
		$data[] = array( $scheme->struct, 'stack' );
		$data[] = array( $scheme->struct[ 'key' ], 'stack' );
		return $data;
	}
	
	/**
	 * @dataProvider dataProvider
	 */
	public function testMain( NScheme_Structure_Base $base, $key )
	{
		$this->assertSame( 'NScheme_Structure_Stack', get_class( $base->{$key} ) );
		
		$retval = $base->{$key}->clear();
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( true, $base->{$key}->isEmpty() );
		$this->assertSame( 0, $base->{$key}->getCount() );
		$this->assertSame( null, $base->{$key}->pop() );
		$this->assertSame( null, $base->{$key}->peek() );
		
		$retval = $base->{$key}->push( 'value1' );
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( false, $base->{$key}->isEmpty() );
		$this->assertSame( 1, $base->{$key}->getCount() );
		$this->assertSame( 'value1', $base->{$key}->peek() );
		
		$retval = $base->{$key}->push( 'value2' );
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( false, $base->{$key}->isEmpty() );
		$this->assertSame( 2, $base->{$key}->getCount() );
		$this->assertSame( 'value2', $base->{$key}->peek() );
		
		$retval = $base->{$key}->pop();
		
		$this->assertSame( 'value2', $retval );
		$this->assertSame( false, $base->{$key}->isEmpty() );
		$this->assertSame( 1, $base->{$key}->getCount() );
		$this->assertSame( 'value1', $base->{$key}->peek() );
		
		$retval = $base->{$key}->pop();
		
		$this->assertSame( 'value1', $retval );
		$this->assertSame( true, $base->{$key}->isEmpty() );
		$this->assertSame( 0, $base->{$key}->getCount() );
		$this->assertSame( null, $base->{$key}->peek() );
		
		$retval = $base->{$key}->pop();
		
		$this->assertSame( null, $retval );
		$this->assertSame( true, $base->{$key}->isEmpty() );
		$this->assertSame( 0, $base->{$key}->getCount() );
		$this->assertSame( null, $base->{$key}->peek() );
	}
	
	/**
	 * @dataProvider dataProvider
	 */
	public function testAltSyntax( NScheme_Structure_Base $base, $key )
	{
		$base->{$key}->clear();
		
		$this->assertSame( 0, count( $base->{$key} ) );
		
		$base->{$key}[] = 'value1';
		$base->{$key}[] = 'value2';
		
		$this->assertSame( 2, count( $base->{$key} ) );
		
		$values = array();
		foreach ( $base->{$key} as $value )
		{
			$values[] = $value;
			if ( $value == 'value1' )
			{
				$base->{$key}[] = 'value3';
			}
		}
		
		$this->assertSame( array( 'value2', 'value1', 'value3' ), $values );
		$this->assertSame( 0, count( $base->{$key} ) );
	}
}