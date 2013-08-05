<?php namespace RobinMalfait\Formgenerator\Facades;

use Illuminate\Support\Facades\Facade;

class Formgenerator extends Facade{

	/**
	 * Gets the registerd name of the component.
	 *
	 * @return string
	 */

	protected static function getFacadeAccessor() { return 'Formgenerator'; }
}