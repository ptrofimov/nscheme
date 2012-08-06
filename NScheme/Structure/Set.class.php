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