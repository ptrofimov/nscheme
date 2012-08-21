<?php
/**
 * Stack NScheme structure
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Structure_Stack implements ArrayAccess
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
	
	public function offsetSet( $offset, $value )
	{
		if ( !is_null( $offset ) )
		{
			throw new NScheme_Exception( 'No random access in stack' );
		}
		$this->push( $value );
	}
	
	public function offsetExists( $offset )
	{
		throw new NScheme_Exception( 'No random access in stack' );
	}
	
	public function offsetUnset( $offset )
	{
		throw new NScheme_Exception( 'No random access in stack' );
	}
	
	public function offsetGet( $offset )
	{
		throw new NScheme_Exception( 'No random access in stack' );
	}
}