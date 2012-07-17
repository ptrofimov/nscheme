<?php
require_once ( 'config.php' );
require_once ( 'NScheme.class.php' );

class MyScheme extends NScheme
{
	public function __construct()
	{
		parent::__construct( SERVER );
		$this->_allowDirectAccess( true );
		$this->_scheme( array( 'title'/*, 'logins' => 'set', 'dict' => 'hash', 'users' => array( 'name', 'email' ) */) );
	}
}

$myScheme = new MyScheme();

var_dump( $myScheme->title->set( 'MyTitle' ) );
var_dump( $myScheme->title->get() );

// var_dump( $myScheme->title = 'MyTitle 2' );
// var_dump( $myScheme->title );

// var_dump( $myScheme->title->set( 'MyTitle' ) );
// var_dump( $myScheme->title->get() );

// var_dump( $myScheme->logins->add( 'new-user' ) );
// var_dump( $myScheme->logins->exists( 'new-user' ) );
// var_dump( $myScheme->logins->exists( 'old-user' ) );
// var_dump( $myScheme->logins->get() );

// var_dump( $myScheme->dict->set( 'new-user', 'data' ) );
// var_dump( $myScheme->dict->exists( 'new-user' ) );
// var_dump( $myScheme->dict->get( 'new-user' ) );

//$myScheme->users->get( 'new-user' )->permits->exists( 'edit_type_area' );

//$myScheme->users[ 'new-user' ]->permits->exists( 'wer' );

//$myScheme->users[] = 'ert';