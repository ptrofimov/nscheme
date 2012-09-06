<?php
/**
 * Adapter for LiteMemcache client (Memcached storage)
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
class NScheme_Adapter_LiteMemcache extends NScheme_Adapter
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
		return $this->_client->del( $key . ':top' );
	}
	
	public function stackPush( $key, $value )
	{
		$top = ( int ) $this->_client->get( $key . ':top' );
		$this->_client->set( $key . ':' . $top, $value );
		$this->_client->set( $key . ':top', $top + 1 );
	}
	
	public function stackPeek( $key )
	{
		$top = ( int ) $this->_client->get( $key . ':top' );
		return $top ? $this->_client->get( $key . ':' . ( $top - 1 ) ) : null;
	}
	
	public function stackPop( $key )
	{
		$value = null;
		$top = ( int ) $this->_client->get( $key . ':top' );
		if ( $top )
		{
			$value = $this->_client->get( $key . ':' . ( $top - 1 ) );
			$this->_client->set( $key . ':top', $top - 1 );
		}
		return $value;
	}
	
	public function stackGetCount( $key )
	{
		return ( int ) $this->_client->get( $key . ':top' );
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
}