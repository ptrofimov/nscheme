<?php
/**
 * Adapter for TinyRedisClient client (Redis storage)
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
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