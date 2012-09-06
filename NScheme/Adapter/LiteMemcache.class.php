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
		$keys = $this->_client->get( array( $key . ':start', $key . ':end' ) );
		for( $i = $keys[ $key . ':start' ]; $i < $keys[ $key . ':end' ]; $i++ )
		{
			$this->_client->del( $key . ':' . $i );
		}
		$this->_client->set( $key . ':start', 0 );
		$this->_client->set( $key . ':end', 0 );
	}
	
	public function queueGetCount( $key )
	{
		$keys = $this->_client->get( array( $key . ':start', $key . ':end' ) );
		return ( int ) ( $keys[ $key . ':end' ] - $keys[ $key . ':start' ] );
	}
	
	public function queueShift( $key )
	{
		$value = null;
		$keys = $this->_client->get( array( $key . ':start', $key . ':end' ) );
		if ( $keys[ $key . ':start' ] < $keys[ $key . ':end' ] )
		{
			$value = $this->_client->get( $key . ':' . $keys[ $key . ':start' ] );
			$this->_client->del( $key . ':' . $keys[ $key . ':start' ] );
			$this->_client->set( $key . ':start', $keys[ $key . ':start' ] + 1 );
		}
		return $value;
	}
	
	public function queuePeek( $key )
	{
		$value = null;
		$keys = $this->_client->get( array( $key . ':start', $key . ':end' ) );
		if ( $keys[ $key . ':start' ] < $keys[ $key . ':end' ] )
		{
			$value = $this->_client->get( $key . ':' . $keys[ $key . ':start' ] );
		}
		return $value;
	}
	
	public function queuePush( $key, $value )
	{
		$end = ( int ) $this->_client->get( $key . ':end' );
		$this->_client->set( $key . ':' . $end, $value );
		$this->_client->set( $key . ':end', $end + 1 );
	}
	
	public function setClear( $key )
	{
		$set = ( string ) $this->_client->get( $key . ':set' );
		$hashes = explode( ',', $set );
		if ( $set )
		{
			foreach ( $hashes as $hash )
			{
				$this->_client->del( $key . ':' . $hash );
			}
		}
		$this->_client->set( $key . ':set', '' );
	}
	
	public function setGetCount( $key )
	{
		$set = ( string ) $this->_client->get( $key . ':set' );
		$hashes = explode( ',', $set );
		return $set ? count( $hashes ) : 0;
	}
	
	public function setExists( $key, $value )
	{
		return ( bool ) $this->_client->get( $key . ':' . md5( $value ) );
	}
	
	public function setAdd( $key, $value )
	{
		$found = false;
		$set = ( string ) $this->_client->get( $key . ':set' );
		$hashes = $set ? explode( ',', $set ) : array();
		$hash = md5( $value );
		for( $i = 0, $ic = count( $hashes ); $i < $ic; $i++ )
		{
			if ( $hashes[ $i ] == $hash )
			{
				$found = true;
				break;
			}
		}
		if ( !$found )
		{
			$hashes[] = $hash;
			$this->_client->set( $key . ':set', implode( ',', $hashes ) );
			$this->_client->set( $key . ':' . $hash, $value );
		}
	}
	
	public function setDel( $key, $value )
	{
		$found = false;
		$set = ( string ) $this->_client->get( $key . ':set' );
		$hashes = $set ? explode( ',', $set ) : array();
		$hash = md5( $value );
		for( $i = 0, $ic = count( $hashes ); $i < $ic; $i++ )
		{
			if ( $hashes[ $i ] == $hash )
			{
				unset( $hashes[ $i ] );
				$found = true;
				break;
			}
		}
		if ( $found )
		{
			$this->_client->set( $key . ':set', implode( ',', $hashes ) );
			$this->_client->del( $key . ':' . $hash );
		}
	}
	
	public function setGet( $key )
	{
		$values = array();
		$set = ( string ) $this->_client->get( $key . ':set' );
		$hashes = $set ? explode( ',', $set ) : array();
		foreach ( $hashes as $hash )
		{
			$values[] = $this->_client->get( $key . ':' . $hash );
		}
		return $values;
	}
	
	public function hashSet( $key, $value )
	{
		return $this->_client->set( md5( $key ), $value );
	}
	
	public function hashGet( $key )
	{
		return $this->_client->get( md5( $key ) );
	}
	
	public function hashDel( $key )
	{
		return $this->_client->del( md5( $key ) );
	}
}