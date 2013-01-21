<?php

class KlearMatrix_Model_Field_Select_Mapper extends KlearMatrix_Model_Field_Select_Abstract
{
    protected $_showOnSelect = array();
    protected $_hideOnSelect = array();

    protected $_js = array(
        "/js/plugins/jquery.klearmatrix.select.js"
    );

    public function init()
    {
        if ($this->_dynamicDataLoading() === true) {

            //Nothing to do
            return;
        }

        $mapperName = $this->_config->getProperty("config")->mapperName;
        $dataMapper = new $mapperName;

        $where = $this->_getFilterWhere();
        $order = $this->_config->getProperty('config')->order;
        $results = $dataMapper->fetchList($where, $order);
        $this->_setOptions($results);
    }


    /**
     * return bool
     */
    protected function _dynamicDataLoading()
    {
        if (isset($this->_column->getKlearConfig()->getRaw()->decorators)) {

            $selfClassName = get_class($this);
            $classBasePath = substr($selfClassName, 0, strrpos($selfClassName, '_') + 1);
            $decoratorClassBaseName = $classBasePath . 'Decorator_';

            $decorators = $this->_column->getKlearConfig()->getRaw()->decorators;
            foreach ($decorators as $decorator) {

                $decoratorClassName = $decoratorClassBaseName . ucfirst($decorator->key());

                if (class_exists($decoratorClassName) && $decoratorClassName::DYNAMIC_DATA_LOADING) {

                    return true;
                }
            }
        }

        return false;
    }

    protected function _getFilterWhere()
    {
        $filterClassName = $this->_config->getProperty('config')->filterClass;
        if ($filterClassName) {

            $filter = new $filterClassName;

            if ($filter->setRouteDispatcher($this->_column->getRouteDispatcher())) {

                return $filter->getCondition();
            }
        }
        return null;
    }

    protected function _setOptions($results)
    {
        if ($results) {
            foreach ($results as $dataModel) {
                $this->_keys[] = $dataModel->getPrimaryKey();
                $this->_items[] = $this->_getItemValue($dataModel);
                $this->_initVisualFilter($dataModel);
            }
        }
    }

    protected function _getItemValue($dataModel)
    {
        $customValueMethod = $this->_config->getProperty('config')->customValueMethod;
        if ($customValueMethod) {
            return $dataModel->$customValueMethod();
        }

        $fields = $this->_getFields();
        $fieldsTemplate = $this->_getFieldsTemplate();
        $replace = array();
        foreach ($fields as $fieldName) {
            $getter = 'get' . ucfirst($dataModel->columnNameToVar($fieldName));
            $replace['%' . $fieldName . '%'] = $dataModel->$getter();
        }

        return str_replace(array_keys($replace), $replace, $fieldsTemplate);
    }

    protected function _getFields()
    {
        $fieldName = $this->_config->getProperty('config')->fieldName;

        if (!is_object($fieldName)) {
            return array($fieldName);
        }

        $fieldConfig = new Klear_Model_ConfigParser();
        $fieldConfig->setConfig($fieldName);
        return $fieldConfig->getProperty("fields");
    }

    protected function _getFieldsTemplate()
    {
        $fieldName = $this->_config->getProperty('config')->fieldName;

        if (!is_object($fieldName)) {
            return '%' . $fieldName . '%';
        }

        $fieldConfig = new Klear_Model_ConfigParser();
        $fieldConfig->setConfig($fieldName);
        return $fieldConfig->getProperty("template");
    }

    public function _initVisualFilter($dataModel)
    {
        $visualFilter = $this->_config->getProperty('config')->visualFilter;

        if ($visualFilter) {

            foreach ($visualFilter as $key => $config) {

                if ($this->_config->getProperty("null")) {

                    if ($config->null) {

                        $this->_showOnSelect['__null__'] = $config->null->show;
                        $this->_hideOnSelect['__null__'] = $config->null->hide;

                    } else {

                        $this->_showOnSelect['__null__'] = array();
                        $this->_hideOnSelect['__null__'] = array();
                    }
                }

                $getter = 'get' . ucfirst($dataModel->columnNameToVar($key));
                $value = $dataModel->$getter();

                if ($config->$value) {

                    $this->_showOnSelect[$dataModel->getPrimaryKey()] = $config->$value->show;
                    $this->_hideOnSelect[$dataModel->getPrimaryKey()] = $config->$value->hide;
                }
            }
        }
    }

    public function getExtraJavascript()
    {
        return $this->_js;
    }
}

//EOF