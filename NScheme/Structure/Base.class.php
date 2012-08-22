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
			throw new Exception( sprintf( 'Key "%s" not found', $key ) );
		}
		if ( is_array( $this->_scheme[ $key ] ) )
		{
			return new NScheme_Structure_Base( $this->_client, $this->_scheme[ $key ], array_merge( $this->_path, 
				array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'hash' )
		{
			return new NScheme_Structure_Base( $this->_client, array(), array_merge( $this->_path, array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'set' )
		{
			return new NScheme_Structure_Set( $this->_client, array_merge( $this->_path, array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'queue' )
		{
			return new NScheme_Structure_Queue( $this->_client, array_merge( $this->_path, array( $key ) ) );
		}
		elseif ( $this->_scheme[ $key ] == 'stack' )
		{
			if ( !isset( $this->_instances[ $key ] ) )
			{
				$this->_instances[ $key ] = new NScheme_Structure_Stack( $this->_client, array_merge( $this->_path, 
					array( $key ) ) );
			}
			return $this->_instances[ $key ];
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
		return new NScheme_Structure_Base( $this->_client, $this->_scheme, $path );
	}
}