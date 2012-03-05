<?php


// TODO: Crear clase abstracta
class KlearMatrix_Model_Field_Number_Spinner {

    protected $_max = false;
    protected $_min = false;
    protected $_step = 1;
    
    protected $_config;
    
    protected $_js = array(
            "/js/plugins/jquery.ui.spinner.js"
    );
    
    protected $_css = array(
            "/css/jquery.ui.spinner.css"            
    );
    
    public function setConfig($config) {
        $this->_config = $config;
        return $this;
    }
    

    public function init() {
        if (isset($this->_config->min)) {
            $this->_min = $this->_config->min;
        }
        
        if (isset($this->_config->max)) {
            $this->_max = $this->_config->max;
        }
        
        if (isset($this->_config->step)) {
            $this->_step = $this->_config->step;
        }
        
        return $this;
    }
    
    public function getExtraJavascript() {
        return $this->_js;
        
    }
    
    public function getExtraCss() {
        return $this->_css;
    
    }
    
    public function getConfig() {
        return 
            array(
                    "plugin"=>'spinner',
                    "attributes" => 
                        array(
                          'min' => $this->_min,
                          'max' => $this->_max,
                          'step'=> $this->_step
                        )
                    
        );
    }
    

    
}
