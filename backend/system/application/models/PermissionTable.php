<?php

/**
 * PermissionTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PermissionTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object PermissionTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Permission');
    }
}