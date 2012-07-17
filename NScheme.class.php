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

class NSchemeHash extends NSchemeBase
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
}

class NScheme
{
	private $_client, $_direct, $_types, $_list;
	
	public function __construct( $server )
	{
		$this->_client = new TinyRedisClient( $server );
		$this->_direct = false;
		$this->_types = array( 'value' => 'NSchemeValue', 'set' => 'NSchemeSet', 'hash' => 'NSchemeHash' );
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
	
	protected function _scheme( array $scheme )
	{
		foreach ( $scheme as $key => $value )
		{
			if ( is_array( $value ) )
			{
				$value = $value[ 0 ];
			}
			if ( !isset( $this->_types[ $value ] ) )
			{
				throw new Exception( sprintf( 'Invalid data type "%s"', $value ) );
			}
			$this->_list[ $key ] = new $this->_types[ $value ]( $this->_client, $key );
		}
	}
	
	public function __get( $key )
	{
		if ( !isset( $this->_list[ $key ] ) )
		{
			throw new Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		return $this->_list[ $key ];
	}
	
	public function __set( $key, $value )
	{
		throw new Exception( 'Forbidden to set properties' );
	}
	
	public function __isset( $key )
	{
		return isset( $this->_list[ $key ] );
	}
	
	public function __unset( $key )
	{
		throw new Exception( 'Forbidden to unset properties' );
	}
}