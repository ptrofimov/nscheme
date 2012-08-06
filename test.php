<?php
require_once ( 'config.php' );
require_once ( 'TinyRedisClient.class.php' );
require_once ( 'NScheme/NScheme.class.php' );

class MyScheme extends NScheme
{
	public function __construct()
	{
		parent::__construct( new TinyRedisClient( SERVER ) );
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

$my = new MyScheme();

var_dump( $my->value = 1 );
var_dump( $my->value );
var_dump( $my->hash[ 'key' ] = 1 );
var_dump( $my->hash[ 'key' ] );
var_dump( $my->value2[ 'sdf' ]->value21 = 1 );
var_dump( $my->value2[ 'sdf' ]->value21 );
var_dump( $my->value2->value21 = 1 );
var_dump( $my->value2->value21 );

var_dump( $my->set->add( 'value' ) );
var_dump( $my->set->exists( 'value' ) );
var_dump( $my->set->get() );

var_dump( $my->queue->push( 'value' ) );
var_dump( $my->queue->isEmpty() );
var_dump( $my->queue->shift() );

var_dump( $my->stack->push( 'stack_value' ) );
var_dump( $my->stack->pop() );
var_dump( $my->stack->push( 'stack_value' ) );
