<?php
/**
 * @author Petr Trofimov
 */
class TestScheme extends NScheme
{
	public function __construct( $client )
	{
		parent::__construct( $client );
		$this->_define( 
			array( 
				'value', 
				'stack' => 'stack', 
				'queue' => 'queue', 
				'set' => 'set', 
				'hash' => 'hash', 
				'struct' => array( 'value', 'stack' => 'stack', 'queue' => 'queue', 'set' => 'set', 'hash' => 'hash' ) ) );
	}
}