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
				'value2' => array( 'value21' ), 
				'hash' => 'hash', 
				'set' => 'set', 
				'stack' => 'stack', 
				'queue' => 'queue' ) );
	}
}