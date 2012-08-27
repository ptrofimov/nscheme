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
	
	public function stackClear( $key )
	{
		$key = new Rediska_Key( $key );
		return $key->delete();
	}
	
	public function stackPush( $key, $value )
	{
		$list = new Rediska_Key_List( $key );
		return $list->append( $value );
	}
	
	public function stackPeek( $key )
	{
		$list = new Rediska_Key_List( $key );
		$result = $list->getValues( -1, 1 );
		return !empty( $result ) ? reset( $result ) : null;
	}
	
	public function stackPop( $key )
	{
		$list = new Rediska_Key_List( $key );
		return $list->pop();
	}
	
	public function stackGetCount( $key )
	{
		$list = new Rediska_Key_List( $key );
		return ( int ) $list->count();
	}
	
	public function queueClear( $key )
	{
		$key = new Rediska_Key( $key );
		return $key->delete();
	}
	
	public function queueGetCount( $key )
	{
		$list = new Rediska_Key_List( $key );
		return ( int ) $list->count();
	}
	
	public function queueShift( $key )
	{
		$list = new Rediska_Key_List( $key );
		return $list->shift();
	}
	
	public function queuePeek( $key )
	{
		$list = new Rediska_Key_List( $key );
		$result = $list->getValues( 0, 1 );
		return !empty( $result ) ? reset( $result ) : null;
	}
	
	public function queuePush( $key, $value )
	{
		$list = new Rediska_Key_List( $key );
		return $list->append( $value );
	}
}