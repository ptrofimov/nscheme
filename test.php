<?php
require_once ( 'config.php' );
require_once ( 'NScheme.class.php' );

/*$ts1 = microtime( true );
$count = 1000000;
for( $i = 0; $i < $count; $i++ )
{
	$hash = crc32( 'string' . $i );
}
$ts2 = microtime( true );
var_dump( 1 / ( ( $ts2 - $ts1 ) / $count ) );
exit();*/

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
		//$this->_define( 'some_set', 'set' );
		//$this->_define( 'some_hash', 'hash' );
		//$this->_define( 'some_queue', 'queue' );
		//$this->_define( 'some_stack', 'stack' );
		//$this->_define( 'level1', 'value', array( 'value' ) );
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


//var_dump( $my->value[ 'key' ] = 1 );
//var_dump( $my->value[ 'key' ] );

/*var_dump( $my->some_value = 'value' );
var_dump( $my->some_value );



var_dump( $my->some_hash[ 'key' ] = 'value' );
var_dump( isset( $my->some_hash[ 'key' ] ) );
var_dump( $my->some_hash[ 'key' ] );



var_dump( $my->level1->value = 'level1' );
//var_dump( $my->value_of_stuct->value );*/

// $my->value->key='value; value.key=value
// $my->hash['sdf']->key=value hash243480980:key=value
// $my->stack->push('sdf') stack:0=value stack:top++
