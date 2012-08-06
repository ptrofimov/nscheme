<?php
/**
 * Base NScheme class
 * 
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
require_once ( 'Exception.class.php' );
require_once ( 'Adapter.class.php' );
require_once ( 'Structure/Base.class.php' );

class NScheme extends NScheme_Structure_Base
{
	/**
	 * @var NScheme_Adapter
	 */
	private $_client;
	
	public function __construct( $client )
	{
		if ( !is_object( $client ) )
		{
			throw new NScheme_Exception( 'First argument must be an object' );
		}
		elseif ( get_class( $client ) == 'TinyRedisClient' )
		{
			require_once ( 'Adapter/TinyRedisClient.class.php' );
			$this->_client = new NScheme_Adapter_TinyRedisClient( $client );
		}
		else
		{
			throw new NScheme_Exception( sprintf( 'Unknown NoSQL client "%s"', get_class( $client ) ) );
		}
	}
	
	protected function _define( array $scheme )
	{
		parent::__construct( $this->_client, $scheme, array() );
	}
}