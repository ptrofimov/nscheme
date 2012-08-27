<?php
/**
 * Adapter for TinyRedisClient client (Redis storage)
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Adapter_Rediska extends NScheme_Adapter
{
	public function valueSet( $key, $value )
	{
		return $this->_client->set( $key, $value );
	}
	
	public function valueGet( $key )
	{
		return $this->_client->get( $key );
	}
	
	/*  old methods*/
	
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
	
	public function del( $key )
	{
		return $this->_client->del( $key );
	}
	
	public function lrange( $key, $start, $stop )
	{
		return $this->_client->lrange( $key, $start, $stop );
	}
	
	public function scard( $key )
	{
		return $this->_client->scard( $key );
	}
	
	public function srem( $key, $value )
	{
		return $this->_client->srem( $key, $value );
	}
}