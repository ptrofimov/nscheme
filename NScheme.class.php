<?php
require_once ( 'TinyRedisClient.class.php' );

class NSchemeBase
{
	protected $_client, $_key;
	
	public function __construct( $client, $key )
	{
		$this->_client = $client;
		$this->_key = $key;
	}
	
	public function getKey()
	{
		return $this->_key;
	}
}

class NSchemeValue extends NSchemeBase
{
	public function set( $value )
	{
		return $this->_client->set( $this->_key, $value );
	}
	
	public function get()
	{
		return $this->_client->get( $this->_key );
	}
	
	public function del()
	{
		return $this->_client->del( $this->_key );
	}
	
	public function __toString()
	{
		return $this->get();
	}
}

class NSchemeSet extends NSchemeBase
{
	public function add( $value )
	{
		return $this->_client->sadd( $this->_key, $value );
	}
	
	public function exists( $value )
	{
		return $this->_client->sismember( $this->_key, $value );
	}
	
	public function get()
	{
		return $this->_client->smembers( $this->_key );
	}
}

class NSchemeQueue extends NSchemeBase
{
	public function push( $value )
	{
		return $this->_client->rpush( $this->_key, $value );
	}
	
	public function isEmpty()
	{
		return $this->_client->llen( $this->_key ) == 0;
	}
	
	public function shift()
	{
		return $this->_client->lpop( $this->_key );
	}
}

class NSchemeStack extends NSchemeBase
{
	public function push( $value )
	{
		return $this->_client->rpush( $this->_key, $value );
	}
	
	public function pop()
	{
		return $this->_client->rpop( $this->_key );
	}
}

class NSchemeHash extends NSchemeBase implements ArrayAccess
{
	public function set( $key, $value )
	{
		return $this->_client->hset( $this->_key, $key, $value );
	}
	
	public function exists( $key )
	{
		return $this->_client->hexists( $this->_key, $key );
	}
	
	public function get( $key )
	{
		return $this->_client->hget( $this->_key, $key );
	}
	
	public function offsetSet( $offset, $value )
	{
		return $this->set( $offset, $value );
	}
	public function offsetExists( $offset )
	{
		return $this->exists( $offset );
	}
	public function offsetUnset( $offset )
	{
		return $this->set( $offset, null );
	}
	public function offsetGet( $offset )
	{
		return $this->get( $offset );
	}
}

class NScheme
{
	private $_client, $_direct, $_types, $_list;
	
	public function __construct( $server )
	{
		$this->_client = new TinyRedisClient( $server );
		$this->_direct = false;
		$this->_types = array( 
			'value' => 'NSchemeValue', 
			'set' => 'NSchemeSet', 
			'hash' => 'NSchemeHash', 
			'queue' => 'NSchemeQueue', 
			'stack' => 'NSchemeStack' );
		$this->_list = array();
	}
	
	protected function _allowDirectAccess( $flag )
	{
		$this->_direct = ( bool ) $flag;
	}
	
	public function getClient()
	{
		if ( $this->_direct )
		{
			return $this->_client;
		}
	}
	
	protected function _define( $key, $type = 'value', $value = 'value' )
	{
		if ( !isset( $this->_types[ $type ] ) )
		{
			throw new Exception( sprintf( 'Invalid data type "%s"', $type ) );
		}
		$this->_list[ $key ] = new $this->_types[ $type ]( $this->_client, $key );
	}
	
	public function __get( $key )
	{
		if ( !isset( $this->_list[ $key ] ) )
		{
			throw new Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		if ( $this->_list[ $key ] instanceof NSchemeValue )
		{
			return $this->_list[ $key ]->get();
		}
		return $this->_list[ $key ];
	}
	
	public function __set( $key, $value )
	{
		if ( !isset( $this->_list[ $key ] ) )
		{
			throw new Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		return $this->_list[ $key ]->set( $value );
	}
	
	public function __isset( $key )
	{
		return isset( $this->_list[ $key ] );
	}
	
	public function __unset( $key )
	{
		if ( !isset( $this->_list[ $key ] ) )
		{
			throw new Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		return $this->_list[ $key ]->del();
	}
}