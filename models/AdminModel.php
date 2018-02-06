<?php

class AdminModel extends DefModel
{
    public function getAllUsers()
    {
        $this->_db = $this->DbConnection();
        $result = $this->selectData($this->_db,
                                array(null,
                                      null
                                ),
                                array('is_accessible'  => 'IS',
                                      'is_admin'       => 'IS'
                                ),
                                array('id',
                                    'login'
                                ),
                                'AND');
        return $result;
    }

    public function givePermissions($ids)
    {
        $this->_db = $this->DbConnection();
        foreach ($ids as $id) {
            $this->updateData($this->_db, 1, 'is_accessible', $id);
        }
    }
}