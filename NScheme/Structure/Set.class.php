<?php
/**
 * Set NScheme structure
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Structure_Set
{
	private $_client, $_path, $_key;
	
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
}