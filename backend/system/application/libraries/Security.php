<?php
/**
 * Fornece funções úteis para manipular o acesso ao sistema
 *
 * @package
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class Security
{
  /**
   * Verifica se um usuário possui um privilégio de acesso a uma determinada
   * permissão.
   * 
   * @param string $name
   * @return boolean
   */
  public static function hasPrivilege($name)
  {
    $context = &get_instance();
    
    $has = false;
    
    // instancia a sessão
    $session = new Zend_Session_Namespace($context->config->item('session_name'),true);

    if (isset($session->login['privileges'][$name]))
    {
      $has = true;
    }
    
    return $has;
  }
}
?>
