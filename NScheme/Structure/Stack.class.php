<?php
/**
 * Stack NScheme structure
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Structure_Stack implements ArrayAccess, Iterator, Countable
{
	private $_client, $_path, $_key, $_value, $_end;
	
	public function __construct( $client, array $path )
	{
		$this->_client = $client;
		$this->_path = $path;
		$this->_key = implode( ':', $path );
		$this->_loaded = false;
	}
	
	public function clear()
	{
		$this->_client->del( $this->_key );
		return $this;
	}
	
	public function push( $value )
	{
		$this->_client->rpush( $this->_key, $value );
		return $this;
	}
	
	public function peek()
	{
		$list = $this->_client->lrange( $this->_key, -1, 1 );
		return !empty( $list ) ? reset( $list ) : null;
	}
	
	public function pop()
	{
		return $this->_client->rpop( $this->_key );
	}
	
	public function isEmpty()
	{
		return $this->getCount() == 0;
	}
	
	public function getCount()
	{
		return ( int ) $this->_client->llen( $this->_key );
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
		$this->next();
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function current()
	{
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
		$this->_end = $this->isEmpty();
		$this->_value = $this->_end ? null : $this->pop();
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function valid()
	{
		return !$this->_end;
	}
	
	/**
	 * Implementation of Countable methods
	 */
	public function count()
	{
		return $this->getCount();
	}
}