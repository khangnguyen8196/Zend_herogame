<?php
/**
 * DbTableRow
 *
 */
class DbTableRow extends Zend_Db_Table_Row_Abstract
{
    
    /**
     * Pre-insert logic
     * Automatically insert created_by, date_created and date_updated
     */
    protected function _insert()
    {
        $this->date_created = new Zend_Db_Expr("NOW()");
        return parent::_insert();
    }
    
    /**
     * Pre-update logic
     * Automatically update date_updated
     *
     */
    protected function _update()
    {
        $this->date_updated = new Zend_Db_Expr("NOW()");
        parent::_update();
    }

    /**
     * Pre-delete logic
     * Automatically update date_updated
     *
     */
    protected function _delete()
    {
        parent::_delete();
    }
}