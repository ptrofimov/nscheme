<?php
/**
 * @author Petr Trofimov
 */
class MyScheme extends NScheme
{
	public function __construct()
	{
		parent::__construct( new TinyRedisClient( 'localhost:6379' ) );
		$this->_define( 
			array( 
				'value', 
				'stack' => 'stack', 
				'queue' => 'queue', 
				'set' => 'set', 
				'hash' => 'hash', 
				'struct' => array( 'value', 'stack' => 'stack' ) ) );
	}
}