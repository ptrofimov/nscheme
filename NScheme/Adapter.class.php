<?php
/**
 * Base class for client adapters
 *
 * @link https://github.com/ptrofimov/nscheme
 * @author Petr Trofimov
 */
abstract class NScheme_Adapter
{
	protected $_client;
	
	public function __construct( $client )
	{
		$this->_client = $client;
	}
}