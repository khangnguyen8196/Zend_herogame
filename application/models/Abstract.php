<?php
/**
 * Model abstract
 *
 */
class Model_Abstract extends Zend_Db_Table_Abstract {
    
    /**
     * Add
     * @param array $data
     * @return Ambigous <mixed, multitype:>
     */
    public function add( $data ) {
        $newRow = $this->createRow( $data );
        return $newRow->save();
    }
    
    /**
     * Edit
     * @param array $data
     * @return number
     */
    public function edit( $data ) {
        return $this->update( $data, "{$this->_primary}={$data[$this->_primary]}" );
    }

}
