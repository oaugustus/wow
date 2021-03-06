<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Privilege', 'main');

/**
 * BasePrivilege
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $group_id
 * @property integer $permission_id
 * @property Group $Group
 * @property Permission $Permission
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePrivilege extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('privilege');
        $this->hasColumn('group_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => false,
             'length' => '4',
             ));
        $this->hasColumn('permission_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => false,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Group', array(
             'local' => 'group_id',
             'foreign' => 'id'));

        $this->hasOne('Permission', array(
             'local' => 'permission_id',
             'foreign' => 'id'));
    }
}