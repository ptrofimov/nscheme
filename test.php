<?php
require_once ( 'config.php' );
require_once ( 'NScheme.class.php' );

class MyScheme extends NScheme
{
	public function __construct()
	{
		parent::__construct( SERVER );
		$this->_define( 'some_value' );
		$this->_define( 'some_set', 'set' );
		$this->_define( 'some_hash', 'hash' );
		//$this->_define( 'some_queue', 'queue' );
		//$this->_define( 'some_stack', 'stack' );
	}
}

$my = new MyScheme();

var_dump( $my->some_value = 'value' );
var_dump( $my->some_value );

var_dump( $my->some_set->add( 'value' ) );
var_dump( $my->some_set->exists( 'value' ) );
var_dump( $my->some_set->get() );

var_dump( $my->some_hash[ 'key' ] = 'value' );
var_dump( isset( $my->some_hash[ 'key' ] ) );
var_dump( $my->some_hash[ 'key' ] );