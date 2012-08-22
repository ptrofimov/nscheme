<?php
/**
 * Stack NScheme structure
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Structure_Stack implements ArrayAccess, Iterator
{
	private $_client, $_path, $_key, $_value, $_loaded, $_end;
	
	public function __construct( $client, array $path )
	{
		$this->_client = $client;
		$this->_path = $path;
		$this->_key = implode( ':', $path );
		$this->_loaded = false;
	}
	
	public function clear()
	{
		return $this;
	}
	
	public function push( $value )
	{
		$this->_client->rpush( $this->_key, $value );
		return $this;
	}
	
	public function pop()
	{
		return $this->_client->rpop( $this->_key );
	}
	
	public function isEmpty()
	{
	}
	
	public function getCount()
	{
	}
	
	/**
	 * Implementation of ArrayAccess methods
	 */
	public function offsetSet( $offset, $value )
	{
		if ( !is_null( $offset ) )
		{
			throw new NScheme_Exception( 'No random access in stack' );
		}
		$this->push( $value );
	}
	
	/**
	 * Implementation of ArrayAccess methods
	 */
	public function offsetExists( $offset )
	{
		throw new NScheme_Exception( 'No random access in stack' );
	}
	
	/**
	 * Implementation of ArrayAccess methods
	 */
	public function offsetUnset( $offset )
	{
		throw new NScheme_Exception( 'No random access in stack' );
	}
	
	/**
	 * Implementation of ArrayAccess methods
	 */
	public function offsetGet( $offset )
	{
		throw new NScheme_Exception( 'No random access in stack' );
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function rewind()
	{
		$this->_loaded = false;
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function current()
	{
		if ( !$this->_loaded )
		{
			$this->_value = $this->pop();
			$this->_end = is_null( $this->_value );
			$this->_loaded = true;
		}
		return $this->_value;
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function key()
	{
		return null;
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function next()
	{
		$this->_value = $this->pop();
		$this->_end = is_null( $this->_value );
		$this->_loaded = true;
		return $this->_value;
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function valid()
	{
		if ( !$this->_loaded )
		{
			$this->_value = $this->pop();
			$this->_end = is_null( $this->_value );
			$this->_loaded = true;
		}
		return !$this->_end;
	}
}