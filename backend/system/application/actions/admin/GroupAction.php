<?php
/**
 * GroupAction
 *
 * This class has been auto-generated by the CI Framework
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id:
 */
class GroupAction extends autoGroupAction
{
  /**
   * Campos e relacionamentos do modelo que serão auto-selecionados pela função
   * index.
   *
   * @access public
   * @var array
   */
  public $autoSelections = array(
    'fields' => array('c.*'),
    'relations' => array()
  );
  
  /**
   * Nome da classe módulo na UI.
   *
   * @var string
   */
  public $basePermission = 'group';

  /**
   * Lista as permissões de um grupo especificado.
   * 
   * @remotable
   *
   * @param Mixed $params
   */
  public function listPrivileges($params)
  {
    if (!isset($params['group_id']))
      return false;
    
    return Doctrine_Core::getTable('Group')->listPrivileges($params['group_id']);
  }

  /**
   * Lista os grupos para a permissão em margens.
   *
   * @remotable
   *
   * @param array $request
   * @return array
   */
  public function listForMargem($request)
  {
    $groups = Doctrine_Core::getTable('Group')->listForMargem($request);

    return $groups;
  }

  /**
   * Remove todos os registros de privilégios antes de salvar o grupo
   *
   * @param Doctrine_Record $record
   */
  public function preSave($record)
  {
    $record->Privilege->delete();
  }
}