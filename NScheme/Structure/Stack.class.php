<?php
/**
 * Stack NScheme structure
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Structure_Stack
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