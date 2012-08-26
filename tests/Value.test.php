<?php
/**
 * @author Petr Trofimov
 */
class ValueTest extends PHPUnit_Framework_TestCase
{
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
	
	public function dataProvider()
	{
		$scheme = new MyScheme();
		
		$data = array();
		$data[] = array( $scheme, 'value' );
		$data[] = array( $scheme->struct, 'value' );
		return $data;
	}
}