<?php
require_once ( 'NScheme.class.php' );

/*$client = new TinyRedisClient( ':6379' );
var_dump( $client->set( 'key', 'value' ) );
var_dump( $client->get( 'key' ) );
var_dump( $client->get( 'key1' ) );
var_dump( $client->keys( '*' ) );
var_dump( $client->incr( 'counter' ) );*/
class MyScheme extends NScheme
{
	public function __construct()
	{
		parent::__construct( ':6379' );
		$this->_allowDirectAccess( false );
		$this->_scheme( 
			array( 'title' => 'value', 'logins' => 'set', 'users' => array( 'hash', array( 'permits' => 'set' ) ) ) );
	}
}

$myScheme = new MyScheme();

var_dump( $myScheme->title->set( 'MyTitle' ) );
var_dump( $myScheme->title->get() );

var_dump( $myScheme->logins->add( 'new-user' ) );
var_dump( $myScheme->logins->exists( 'new-user' ) );
var_dump( $myScheme->logins->exists( 'old-user' ) );
var_dump( $myScheme->logins->get() );

var_dump( $myScheme->users->add( 'new-user' ) );
var_dump( $myScheme->users->exists( 'new-user' ) );
var_dump( $myScheme->users->get() );

//$myScheme->users->get( 'new-user' )->permits->exists( 'type_edit' );

