<?php
class KlearMatrix_Model_MatrixResponse {
	
	protected $_columnWrapper;
	protected $_results;
	protected $_fieldOptionsWrapper = false;
	protected $_pk;
	
	public function setColumnWraper(KlearMatrix_Model_ColumnWrapper $columnWrapper) {
		$this->_columnWrapper = $columnWrapper;
	}
	
	public function setResults($results) {
		
		$this->_results = $results;
	}
	
	public function setPK($pk) {
		$this->_pk = $pk;
	}
	
	public function setFieldOptions(KlearMatrix_Model_FieldOptionsWrapper $fieldOptsWrapper) {
		$this->_fieldOptionsWrapper = $fieldOptsWrapper;
	}
	
	
	/**
	 * Si los resultados (de data) son objetos, los pasa a array (para JSON)
	 * Se eliminan los campos no presentes en el column-wrapper 
	 */
	public function fixResults(KlearMatrix_Model_ResponseItem $screen) {
		
		$colIndexes = array();
		foreach($screen->getVisibleColumnWrapper() as $column) {
			if ($column->isOption()) continue;
			$colIndexes[] = $column->getDbName();
		}
		
		$colIndexes[] = $screen->getPK();
		
		if (!is_array($this->_results)) $this->_results = array($this->_results);

		$_newResults = array();
		
		foreach($this->_results as $result) {

			$_newResult = array();
			
			if ( (is_object($result)) && (get_class($result) == $screen->getModelName()) ) {
				
				foreach($colIndexes as $dbName) {
					$getterFieldName = "get" . $result->columnNameToVar($dbName);
					$_newResult[$dbName] = $result->{$getterFieldName}(); 
				}
				
				$_newResults[] = $_newResult;
			}
			
		}
		
		$this->_results = $_newResults;		
		
	}
	
	public function toJson() {
		
		$ret = array();
		$ret['columns'] = $this->_columnWrapper->toArray();
		
		$ret['values'] = $this->_results;
		$ret['pk'] = $this->_pk;
		if (false !== $this->_fieldOptionsWrapper) {
			$ret['fieldOptions'] = $this->_fieldOptionsWrapper->toArray();
		}

		return $ret;
		
	}
	
}