<?php 


class KlearMatrix_Model_Field_Password_Sha_Salt extends KlearMatrix_Model_Field_Password_Abstract
{
    
    protected function _salt()
    {
        return substr(md5(mt_rand(), false), 0, 8);
    }
    
    public function cryptValue()
    {
        $salt = $this->_salt(); 
        return crypt($this->_clearValue, '$5$rounds=5000$' . $salt . '$');
    }
    
}


    