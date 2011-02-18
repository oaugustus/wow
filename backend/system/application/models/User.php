<?php

/**
 * User
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class User extends BaseUser
{
  /**
   * Register a preSave event to change pass encrypt before
   * save a user record.
   *
   * @param Doctrin_Event $event
   */
  public function preSave($event)
  {
    $this->pass = md5($this->pass);
  }
  
  /**
   * Processa a autenticação do usuário no sistema
   * 
   * @param array $user_data Dados do usuário que deverá ser autenticado
   * 
   * @return Mixed [Boolean/Array] False, caso o usuário não seja autenticado,
   *                               Array com o registro do usuário, caso contrário
   */
  public function authenticate($data)
  {
    // armazena os parâmetros em variáveis locais
    $login = $data['login'];
    $pass  = $data['pass'];

    // tenta recuperar o usuário pelo seu login e senha
    $user = Doctrine::getTable('User')->findByLoginPass($login, $pass);

    // se o registro existir
    if ($user)
    {
      $u = $user;
      // aplica tratamento ao registro
      $user = $user->toArray();
      $user['privileges'] = $u->Group->getPrivileges();
      $user['modules'] = Doctrine_Core::getTable('Application')->loadModules($user['id']);

      // remove campos que não devem ser retornados
      unset($user['pass']);
      unset ($user['group_id']);

      // registra a sessão do usuário
      $session = SessionManager::getSession();
      
      $session->login = $user;
      $session->isLogged = true;
    }

    // retorna o resultado da autenticação
    return $user;
  }

  /**
   * Verifica se existe uma sessão aberta para o usuário
   *
   * @return Mixed[Boolean/Array] Falso caso a sessão não exista, ou a sessão registrada
   */
  public function checkSession()
  {
    // instancia a sessão
    $session = SessionManager::getSession();

    // se o usuário estiver autenticado
    if ($session->isLogged)
    {
      // retorna a sessão de autenticação do usuário
      return $session->login;
    }
    else
    {
      // caso contrário, 
      return false;
    }

  }

  /**
   * Fecha uma sessão aberta do usuário
   *
   */
  public function logout()
  {
    // recupera a instância do contexto de execução do CI
    $context = &get_instance();

    // remove a sessão de autenticação
    Zend_Session::namespaceUnset($context->config->item('session_name'));

    return true;
  }
}