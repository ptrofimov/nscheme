<?php
require_once ( 'TinyRedisClient.class.php' );

class NSchemeBase implements ArrayAccess
{
	private $_client;
	private $_scheme;
	private $_path;
	
	public function __construct( $client, array $scheme, array $path )
	{
		$this->_client = $client;
		$this->_scheme = array();
		foreach ( $scheme as $key => $value )
		{
			if ( is_string( $key ) )
			{
				$this->_scheme[ $key ] = $value;
			}
			else
			{
				$this->_scheme[ $value ] = 'value';
			}
		}
		$this->_path = $path;
	}
	
	public function __get( $key )
	{
		if ( !isset( $this->_scheme[ $key ] ) )
		{
			throw new Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		if ( is_array( $this->_scheme[ $key ] ) )
		{
			return new NSchemeBase( $this->_client, $this->_scheme[ $key ], array_merge( $this->_path, array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'hash' )
		{
			return new NSchemeBase( $this->_client, array(), array_merge( $this->_path, array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'set' )
		{
			return new NSchemeSet( $this->_client, array_merge( $this->_path, array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'queue' )
		{
			return new NSchemeQueue( $this->_client, array_merge( $this->_path, array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'stack' )
		{
			return new NSchemeStack( $this->_client, array_merge( $this->_path, array( $key ) ) );
		}
		else
		{
			$path = array_merge( $this->_path, array( $key ) );
			return $this->_client->get( implode( ':', $path ) );
		}
	}
	
	public function __set( $key, $value )
	{
		if ( !isset( $this->_scheme[ $key ] ) )
		{
			throw new Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		$path = array_merge( $this->_path, array( $key ) );
		return $this->_client->set( implode( ':', $path ), $value );
	}
	
	public function __isset( $key )
	{
		//return isset( $this->_list[ $key ] );
	}
	
	public function __unset( $key )
	{
		/*if ( !isset( $this->_list[ $key ] ) )
		{
			throw new Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		return $this->_list[ $key ]->del();*/
	}
	
	public function offsetSet( $offset, $value )
	{
		//return $this->set( $offset, $value );
		$path = array_merge( $this->_path, array( md5( $offset ) ) );
		return $this->_client->set( implode( ':', $path ), $value );
	
	}
	public function offsetExists( $offset )
	{
		//return $this->exists( $offset );
	}
	public function offsetUnset( $offset )
	{
		//return $this->set( $offset, null );
	}
	
	public function offsetGet( $offset )
	{
		//return $this->get( $offset );
		if ( empty( $this->_scheme ) )
		{
			$path = array_merge( $this->_path, array( md5( $offset ) ) );
			return $this->_client->get( implode( ':', $path ) );
		}
		$path = array_merge( $this->_path, array( md5( $offset ) ) );
		return new NSchemeBase( $this->_client, $this->_scheme, $path );
	}
}

class NSchemeSet
{
	private $_client, $_path, $_key;
	
	public function __construct( $client, array $path )
	{
		$this->_client = $client;
		$this->_path = $path;
		$this->_key = implode( ':', $path );
	}
	
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
	private $_client, $_path, $_key;
	
	public function __construct( $client, array $path )
	{
		$this->_client = $client;
		$this->_path = $path;
		$this->_key = implode( ':', $path );
	}
	
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
	private $_client, $_path, $_key;
	
	public function __construct( $client, array $path )
	{
		$this->_client = $client;
		$this->_path = $path;
		$this->_key = implode( ':', $path );
	}
	
	public function push( $value )
	{
		return $this->_client->rpush( $this->_key, $value );
	}
	
	public function pop()
	{
		return $this->_client->rpop( $this->_key );
	}
}
/*
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
}*/

abstract class NScheme_Adapter
{
	protected $_client;
	
	public function __construct( $client )
	{
		$this->_client = $client;
	}
}

class NScheme_Adapter_TinyRedisClient extends NScheme_Adapter
{
	public function set( $key, $value )
	{
		return $this->_client->set( $key, $value );
	}
	
	public function get( $key )
	{
		return $this->_client->get( $key );
	}
	
	public function sadd( $key, $value )
	{
		return $this->_client->sadd( $key, $value );
	}
	
	public function sismember( $key, $value )
	{
		return $this->_client->sismember( $key, $value );
	}
	
	public function smembers( $key )
	{
		return $this->_client->smembers( $key );
	}
	
	public function rpush( $key, $value )
	{
		return $this->_client->rpush( $key, $value );
	}
	
	public function llen( $key )
	{
		return $this->_client->llen( $key );
	}
	
	public function lpop( $key )
	{
		return $this->_client->lpop( $key );
	}
	
	public function rpop( $key )
	{
		return $this->_client->rpop( $key );
	}
}

class NScheme extends NSchemeBase
{
	//private /*$_direct, $_types, $_list,*/ $_scheme;
	

	private $_client;
	
	public function __construct( $client )
	{
		if ( $client instanceof TinyRedisClient )
		{
			$this->_client = new NScheme_Adapter_TinyRedisClient( $client );
		}
		else
		{
			throw new Exception( sprintf( 'Invalid NoSql client "%s"', get_class( $client ) ) );
		}
		
		//$this->_client = ;
		//$this->_direct = false;
		/*$this->_types = array( 
			'set' => 'NSchemeSet', 
			'queue' => 'NSchemeQueue', 
			'stack' => 'NSchemeStack' );*/
		//$this->_list = array();
			//$this->_scheme = array();
	}
	
	/*protected function _allowDirectAccess( $flag )
	{
		$this->_direct = ( bool ) $flag;
	}
	
	public function getClient()
	{
		if ( $this->_direct )
		{
			return $this->_client;
		}
	}*/
	
	protected function _define( array $scheme )
	{
		/*foreach ( $scheme as $key => $value )
		{
			if ( is_array( $value ) )
			{
				foreach ( $value as $subkey => $subvalue )
				{
					if ( is_string( $subkey ) )
					{
						$this->_check( $subkey, $subvalue );
					}
				}
			}
			elseif ( is_string( $key ) )
			{
				throw new Exception( 'No data structures' );
				/*if ( !isset( $this->_types[ $value ] ) )
			{
				throw new Exception( sprintf( 'Invalid data type "%s"', $value ) );
			}
			}
		}*/
		//$this->_scheme = $scheme;
		parent::__construct( $this->_client, $scheme, array() );
	}
}