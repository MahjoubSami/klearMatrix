<?php


abstract class KlearMatrix_Model_Field_Abstract {

	/**
	 * @var KlearMatrix_Model_Column
	 */
	protected $_column;
	protected $_config;
	
	static protected $_propertyMaster = array("required","pattern","placeholder");
	
	protected $_properties = array();
	
	public function setColumn($column) {
		$this->_column = $column;
		return $this;
	}
	
	/**
	 * Dejar este método vacio, se invocara siempre que se genera desde Column 
	 */
	public function init()
	{
	    $this->_config = $this->_column->getKlearConfig();
	    if (is_object($this->_config)) {
	        foreach (self::$_propertyMaster as $_prop) {
	            $this->_properties[$_prop] = $this->_config->getProperty($_prop);
	        }
	    }

	    return $this;
	}
	
	public function getConfig() {
	  return false;	    
	}
	
	
	public function getProperties() {
	   
	    if (sizeof($this->_properties) <= 0) {
	        return false;
	    }

	    return $this->_properties;
	}
	
	/*
	 * Filtra (y adecua) el valor del campo antes del setter
	 *  
	 */
	public function filterValue($value,$original) {
	    return $value;
	}
	
	/*
	 * Prepara el valor de un campo, después del getter
	 */
	/**
	 * @param mixed $value Valor devuelto por el getter del model
	 * @param object $model Modelo cargado
	 * @return unknown
	 */
	public function prepareValue($value, $model) {
	    return $value;
	}
	
	public function getExtraJavascript() {
	    return false;
	}
	
	public function getExtraCss() {
	    return false;
	}
	
	
}