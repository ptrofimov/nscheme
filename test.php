<?php
require_once ( 'config.php' );
require_once ( 'NScheme.class.php' );

class MyScheme extends NScheme
{
	public function __construct()
	{
		parent::__construct( SERVER );
		$this->_allowDirectAccess( true );
		$this->_scheme( 
			array( 'title', 'users' => array( 'name', 'email' )/*, 'logins' => 'set', 'dict' => 'hash', 'users' => array( 'name', 'email' ) */) );
	}
}

$myScheme = new MyScheme();

var_dump( $myScheme->title = 'MyTitle 2' );
var_dump( $myScheme->title );

var_dump( $myScheme->users['nick'] = array( 'name' => 'Name', 'email' => 'Email' ) );
//$myScheme->users['nick']->name



//sets users[]=5 users->add(5) users->exists(5) users
//lists users[]->
