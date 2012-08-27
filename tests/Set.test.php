<?php
/**
 * @author Petr Trofimov
 */
require_once ( 'include/config.php' );

class SetTest extends PHPUnit_Framework_TestCase
{
	public function dataProvider()
	{
		$data = array();
		foreach ( $GLOBALS[ 'clients' ] as $client )
		{
			$scheme = new TestScheme( $client );
			$data[] = array( $scheme, 'set' );
			$data[] = array( $scheme->struct, 'set' );
			$data[] = array( $scheme->struct[ 'key' ], 'set' );
		}
		return $data;
	}
	
	/**
	 * @dataProvider dataProvider
	 */
	public function testMain( NScheme_Structure_Base $base, $key )
	{
		$this->assertSame( 'NScheme_Structure_Set', get_class( $base->{$key} ) );
		
		$retval = $base->{$key}->clear();
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( true, $base->{$key}->isEmpty() );
		$this->assertSame( 0, $base->{$key}->getCount() );
		$this->assertSame( false, $base->{$key}->exists( 'value1' ) );
		$this->assertSame( false, $base->{$key}->exists( 'value2' ) );
		
		$retval = $base->{$key}->add( 'value1' );
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( false, $base->{$key}->isEmpty() );
		$this->assertSame( 1, $base->{$key}->getCount() );
		$this->assertSame( true, $base->{$key}->exists( 'value1' ) );
		$this->assertSame( false, $base->{$key}->exists( 'value2' ) );
		
		$retval = $base->{$key}->add( 'value2' );
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( false, $base->{$key}->isEmpty() );
		$this->assertSame( 2, $base->{$key}->getCount() );
		$this->assertSame( true, $base->{$key}->exists( 'value1' ) );
		$this->assertSame( true, $base->{$key}->exists( 'value2' ) );
		
		$retval = $base->{$key}->add( 'value2' ); /* uniq test */
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( false, $base->{$key}->isEmpty() );
		$this->assertSame( 2, $base->{$key}->getCount() );
		$this->assertSame( true, $base->{$key}->exists( 'value1' ) );
		$this->assertSame( true, $base->{$key}->exists( 'value2' ) );
		
		$retval = $base->{$key}->del( 'value1' );
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( false, $base->{$key}->isEmpty() );
		$this->assertSame( 1, $base->{$key}->getCount() );
		$this->assertSame( false, $base->{$key}->exists( 'value1' ) );
		$this->assertSame( true, $base->{$key}->exists( 'value2' ) );
		
		$retval = $base->{$key}->del( 'value2' );
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( true, $base->{$key}->isEmpty() );
		$this->assertSame( 0, $base->{$key}->getCount() );
		$this->assertSame( false, $base->{$key}->exists( 'value1' ) );
		$this->assertSame( false, $base->{$key}->exists( 'value2' ) );
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
		
		$this->assertSame( true, isset( $base->{$key}[ 'value1' ] ) );
		
		$values = array();
		foreach ( $base->{$key} as $value )
		{
			$values[] = $value;
		}
		
		sort( $values );
		
		$this->assertSame( array( 'value1', 'value2' ), $values );
		$this->assertSame( 2, count( $base->{$key} ) );
		
		$this->assertSame( true, isset( $base->{$key}[ 'value1' ] ) );
		
		unset( $base->{$key}[ 'value1' ] );
		
		$this->assertSame( 1, count( $base->{$key} ) );
		$this->assertSame( false, isset( $base->{$key}[ 'value1' ] ) );
	}
}