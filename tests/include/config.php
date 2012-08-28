<?php
require_once ( dirname( __FILE__ ) . '/../../NScheme/NScheme.class.php' );
require_once ( 'clients/tinyredisclient/TinyRedisClient.class.php' );
require_once ( 'clients/rediska/Rediska.php' );
require_once ( 'clients/memcached/Memcached.php' );
require_once ( 'TestScheme.class.php' );

$memcached = new Memcached();
$memcached->addServer('localhost', 11211);

$clients = array( 
	//new TinyRedisClient( 'localhost:6379' ), 
	//new Rediska( array( 'servers' => array( array( 'host' => 'localhost', 'port' => 6379 ) ) ) ),
	$memcached,
		 );