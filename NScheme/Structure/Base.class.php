<?php
/**
 * Base NScheme structure
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
require_once ( 'Stack.class.php' );
require_once ( 'Queue.class.php' );
require_once ( 'Set.class.php' );

class NScheme_Structure_Base implements ArrayAccess
{
	private $_client;
	private $_scheme;
	private $_path;
	
	/**
	 * @var array
	 */
	private $_instances;
	
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
		$this->_instances = array();
	}
	
	public function __get( $key )
	{
		if ( !isset( $this->_scheme[ $key ] ) )
		{
			throw new NScheme_Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		if ( is_array( $this->_scheme[ $key ] ) )
		{
			return new NScheme_Structure_Base( $this->_client, $this->_scheme[ $key ], array_merge( 
				$this->_path, array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'hash' )
		{
			if ( !isset( $this->_instances[ $key ] ) )
			{
				$this->_instances[ $key ] = new NScheme_Structure_Base( $this->_client, array(), array_merge( 
					$this->_path, array( $key ) ) );
			}
			return $this->_instances[ $key ];
		}
		elseif ( $this->_scheme[ $key ] == 'set' )
		{
			if ( !isset( $this->_instances[ $key ] ) )
			{
				$this->_instances[ $key ] = new NScheme_Structure_Set( $this->_client, array_merge( 
					$this->_path, array( $key ) ) );
			}
			return $this->_instances[ $key ];
		}
		elseif ( $this->_scheme[ $key ] == 'queue' )
		{
			if ( !isset( $this->_instances[ $key ] ) )
			{
				$this->_instances[ $key ] = new NScheme_Structure_Queue( $this->_client, array_merge( 
					$this->_path, array( $key ) ) );
			}
			return $this->_instances[ $key ];
		}
		elseif ( $this->_scheme[ $key ] == 'stack' )
		{
			if ( !isset( $this->_instances[ $key ] ) )
			{
				$this->_instances[ $key ] = new NScheme_Structure_Stack( $this->_client, array_merge( 
					$this->_path, array( $key ) ) );
			}
			return $this->_instances[ $key ];
		}
		else
		{
			$path = array_merge( $this->_path, array( $key ) );
			return $this->_client->valueGet( implode( ':', $path ) );
		}
	}
	
	public function __set( $key, $value )
	{
		if ( !isset( $this->_scheme[ $key ] ) )
		{
			throw new NScheme_Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		$path = array_merge( $this->_path, array( $key ) );
		return $this->_client->valueSet( implode( ':', $path ), $value );
	}
	
	public function __isset( $key )
	{
		//return $this->exists( $key );
	}
	
	public function __unset( $key )
	{
		//return $this->del( $key );
	}
	
	public function offsetSet( $offset, $value )
	{
		//return $this->set( $offset, $value );
		$path = array_merge( $this->_path, array( md5( $offset ) ) );
		return $this->_client->hashSet( implode( ':', $path ), $value );
	
	}
	public function offsetExists( $offset )
	{
		return $this->exists( $offset );
		//return $this->exists( $offset );
	}
	public function offsetUnset( $offset )
	{
		$path = array_merge( $this->_path, array( md5( $offset ) ) );
		return $this->_client->hashDel( implode( ':', $path ) );
		//return $this->set( $offset, null );
	}
	
	public function offsetGet( $offset )
	{
		//return $this->get( $offset );
		if ( empty( $this->_scheme ) )
		{
			$path = array_merge( $this->_path, array( md5( $offset ) ) );
			return $this->_client->hashGet( implode( ':', $path ) );
		}
		$path = array_merge( $this->_path, array( md5( $offset ) ) );
		return new NScheme_Structure_Base( $this->_client, $this->_scheme, $path );
	}
	
	public function set( $key, $value )
	{
		$this->offsetSet( $key, $value );
		return $this;
	}
	
	public function get( $key )
	{
		return $this->offsetGet( $key );
	}
	
	public function exists( $key )
	{
		return $this->get( $key ) !== null;
	}
	
	public function del( $key )
	{
		$path = array_merge( $this->_path, array( md5( $key ) ) );
		$this->_client->hashDel( implode( ':', $path ) );
		return $this;
	}
}