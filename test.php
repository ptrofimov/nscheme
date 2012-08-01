<?php
require_once ( 'config.php' );
require_once ( 'NScheme.class.php' );

class MyScheme extends NScheme
{
	public function __construct()
	{
		parent::__construct( SERVER );
		$this->_allowDirectAccess( false );
		$this->_define( 'user', 'hash', array( 'name', 'login', 'email' ) );
		$this->_define( 'users', 'set', 'user' );
		$this->_scheme( 
			array( 
				'value', 
				'hash' => 'hash', 
				'user' => array( 'hash', array( 'name', 'login', 'email', 'rights' => 'set' ) ), 
				'users' => array( 'set', 'user' ), 
				'urls' => 'queue' ) );
	}
}

// user is hash [name,login,email]
// users is set of user


// picture as hash of url,alt,height,width
// gallery as set of picture
// galleries is list of gallery
// 
// user is hash of name,login,email
// users is set of user 

$my = new MyScheme();

var_dump( $my->value = 'new_value' );
var_dump( $my->value );
var_dump( isset( $my->value ) );
unset( $my->value );
var_dump( $my->value );
var_dump( isset( $my->value ) );
var_dump( isset( $my->wrongvalue ) );

var_dump( $my->hash[ 'key' ] = 'value' );
var_dump( $my->hash[ 'key' ] );
var_dump( $my->hash[ 'key_wrong' ] );




