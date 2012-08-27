<?php
/**
 * @author Petr Trofimov
 */
require_once ( 'include/config.php' );

class ValueTest extends PHPUnit_Framework_TestCase
{
	public function dataProvider()
	{
		$data = array();
		
		$scheme = new TestScheme( new TinyRedisClient( 'localhost:6379' ) );
		$data[] = array( $scheme, 'value' );
		$data[] = array( $scheme->struct, 'value' );
		$data[] = array( $scheme->struct[ 'key' ], 'value' );
		
		/*$options = array( 
			'namespace' => 'Application_', 
			'servers' => array( array( 'host' => '127.0.0.1', 'port' => 6379 ) ) );*/
		$rediska = new Rediska( array( 'servers' => array( array( 'host' => '127.0.0.1', 'port' => 6379 ) ) ) );
		
		$scheme = new TestScheme( $rediska );
		$data[] = array( $scheme, 'value' );
		$data[] = array( $scheme->struct, 'value' );
		$data[] = array( $scheme->struct[ 'key' ], 'value' );
		
		return $data;
	}
	
	/**
	 * @dataProvider dataProvider
	 */
	public function testMain( NScheme_Structure_Base $base, $key )
	{
		$base->{$key} = 'value1';
		$this->assertSame( 'value1', $base->{$key} );
		
		$base->{$key} = 'value2';
		$this->assertSame( 'value2', $base->{$key} );
	}
}