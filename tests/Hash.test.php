<?php
/**
 * @author Petr Trofimov
 */
require_once ( 'include/config.php' );

class HashTest extends PHPUnit_Framework_TestCase
{
	public function dataProvider()
	{
		$data = array();
		foreach ( $GLOBALS[ 'clients' ] as $client )
		{
			$scheme = new TestScheme( $client );
			$data[] = array( $scheme, 'hash' );
			$data[] = array( $scheme->struct, 'hash' );
			$data[] = array( $scheme->struct[ 'key' ], 'hash' );
		}
		return $data;
	}
	
	/**
	 * @dataProvider dataProvider
	 */
	public function testMain( NScheme_Structure_Base $base, $key )
	{
		$this->assertSame( 'NScheme_Structure_Base', get_class( $base->{$key} ) );
		
		$retval = $base->{$key}->set( 'key1', 'value1' );
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( true, $base->{$key}->exists( 'key1' ) );
		$this->assertSame( 'value1', $base->{$key}->get( 'key1' ) );
		
		$retval = $base->{$key}->del( 'key1' );
		
		$this->assertSame( $base->{$key}, $retval );
		$this->assertSame( false, $base->{$key}->exists( 'key1' ) );
		$this->assertSame( null, $base->{$key}->get( 'key1' ) );
	}
	
	/**
	 * @dataProvider dataProvider
	 */
	public function testAltSyntax( NScheme_Structure_Base $base, $key )
	{
		$base->{$key}[ 'key1' ] = 'value1';
		
		$this->assertSame( true, $base->{$key}->exists( 'key1' ) );
		$this->assertSame( true, isset( $base->{$key}[ 'key1' ] ) );
		$this->assertSame( 'value1', $base->{$key}[ 'key1' ] );
		
		unset( $base->{$key}[ 'key1' ] );
		
		$this->assertSame( false, $base->{$key}->exists( 'key1' ) );
		$this->assertSame( false, isset( $base->{$key}[ 'key1' ] ) );
		$this->assertSame( null, $base->{$key}[ 'key1' ] );
	}
}