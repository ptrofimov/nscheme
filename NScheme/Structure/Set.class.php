<?php
/**
 * Set NScheme structure
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Structure_Set implements ArrayAccess, Iterator, Countable
{
	private $_client, $_path, $_key, $_values, $_count, $_i;
	
	public function __construct( $client, array $path )
	{
		$this->_client = $client;
		$this->_path = $path;
		$this->_key = implode( ':', $path );
	}
	
	public function clear()
	{
		$this->_client->del( $this->_key );
		return $this;
	}
	
	public function add( $value )
	{
		$this->_client->sadd( $this->_key, $value );
		return $this;
	}
	
	public function del( $value )
	{
		$this->_client->srem( $this->_key, $value );
		return $this;
	}
	
	public function exists( $value )
	{
		return ( bool ) $this->_client->sismember( $this->_key, $value );
	}
	
	public function isEmpty()
	{
		return $this->getCount() == 0;
	}
	
	public function getCount()
	{
		return ( int ) $this->_client->scard( $this->_key );
	}
	
	public function get()
	{
		return $this->_client->smembers( $this->_key );
	}
	
	/**
	 * Implementation of ArrayAccess methods
	 */
	public function offsetSet( $offset, $value )
	{
		if ( !is_null( $offset ) )
		{
			throw new NScheme_Exception( 'No random access in set' );
		}
		$this->add( $value );
	}
	
	/**
	 * Implementation of ArrayAccess methods
	 */
	public function offsetExists( $offset )
	{
		return $this->exists( $offset );
	}
	
	/**
	 * Implementation of ArrayAccess methods
	 */
	public function offsetUnset( $offset )
	{
		$this->del( $offset );
	}
	
	/**
	 * Implementation of ArrayAccess methods
	 */
	public function offsetGet( $offset )
	{
		throw new NScheme_Exception( 'No random access in set' );
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function rewind()
	{
		$this->_values = $this->get();
		$this->_count = count( $this->_values );
		$this->_i = 0;
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function current()
	{
		return $this->_values[ $this->_i ];
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
		$this->_i++;
	}
	
	/**
	 * Implementation of Iterator methods
	 */
	public function valid()
	{
		return $this->_i < $this->_count;
	}
	
	/**
	 * Implementation of Countable methods
	 */
	public function count()
	{
		return $this->getCount();
	}
}