<?php
/**
 * @author Petr Trofimov
 */
require_once ( 'include/config.php' );

class QueueTest extends PHPUnit_Framework_TestCase
{
	public function dataProvider()
	{
		$data = array();
		foreach ( $GLOBALS[ 'clients' ] as $client )
		{
			$scheme = new TestScheme( $client );
			$data[] = array( $scheme, 'queue' );
			$data[] = array( $scheme->struct, 'queue' );
			$data[] = array( $scheme->struct[ 'key' ], 'queue' );
		}
		return $data;
	}
	
	/**
	 * @dataProvider dataProvider
	 */
	public function testMain( NScheme_Structure_Base $base, $key )
	{
		$this->assertSame( 'NScheme_Structure_Queue', get_class( $base->{$key} ) );
		
		$retval = $base->{$key}->clear();
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( true, $base->{$key}->isEmpty() );
		$this->assertSame( 0, $base->{$key}->getCount() );
		$this->assertSame( null, $base->{$key}->shift() );
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
		$this->assertSame( 'value1', $base->{$key}->peek() );
		
		$retval = $base->{$key}->shift();
		
		$this->assertSame( 'value1', $retval );
		$this->assertSame( false, $base->{$key}->isEmpty() );
		$this->assertSame( 1, $base->{$key}->getCount() );
		$this->assertSame( 'value2', $base->{$key}->peek() );
		
		$retval = $base->{$key}->shift();
		
		$this->assertSame( 'value2', $retval );
		$this->assertSame( true, $base->{$key}->isEmpty() );
		$this->assertSame( 0, $base->{$key}->getCount() );
		$this->assertSame( null, $base->{$key}->peek() );
		
		$retval = $base->{$key}->shift();
		
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
		
		$this->assertSame( array( 'value1', 'value2', 'value3' ), $values );
		$this->assertSame( 0, count( $base->{$key} ) );
	}
}