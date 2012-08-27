<?php
/**
 * Adapter for TinyRedisClient client (Redis storage)
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Adapter_TinyRedisClient extends NScheme_Adapter
{
	public function valueSet( $key, $value )
	{
		return $this->_client->set( $key, $value );
	}
	
	public function valueGet( $key )
	{
		return $this->_client->get( $key );
	}
	
	public function stackClear( $key )
	{
		return $this->_client->del( $key );
	}
	
	public function stackPush( $key, $value )
	{
		return $this->_client->rpush( $key, $value );
	}
	
	public function stackPeek( $key )
	{
		$list = $this->_client->lrange( $key, -1, 1 );
		return !empty( $list ) ? reset( $list ) : null;
	}
	
	public function stackPop( $key )
	{
		return $this->_client->rpop( $key );
	}
	
	public function stackGetCount( $key )
	{
		return ( int ) $this->_client->llen( $key );
	}
	
	public function queueClear( $key )
	{
		return $this->_client->del( $key );
	}
	
	public function queueGetCount( $key )
	{
		return ( int ) $this->_client->llen( $key );
	}
	
	public function queueShift( $key )
	{
		return $this->_client->lpop( $key );
	}
	
	public function queuePeek( $key )
	{
		$list = $this->_client->lrange( $key, 0, 1 );
		return !empty( $list ) ? reset( $list ) : null;
	}
	
	public function queuePush( $key, $value )
	{
		return $this->_client->rpush( $key, $value );
	}
	
	public function setClear( $key )
	{
		return $this->_client->del( $key );
	}
	
	public function setGetCount( $key )
	{
		return ( int ) $this->_client->scard( $key );
	}
	
	public function setExists( $key, $value )
	{
		return ( bool ) $this->_client->sismember( $key, $value );
	}
	
	public function setAdd( $key, $value )
	{
		return $this->_client->sadd( $key, $value );
	}
	
	public function setDel( $key, $value )
	{
		return $this->_client->srem( $key, $value );
	}
	
	public function setGet( $key )
	{
		return $this->_client->smembers( $key );
	}
	
	public function hashSet( $key, $value )
	{
		return $this->_client->set( $key, $value );
	}
	
	public function hashGet( $key )
	{
		return $this->_client->get( $key );
	}
	
	public function hashDel( $key )
	{
		return $this->_client->del( $key );
	}
	
	/*  old methods*/
	
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