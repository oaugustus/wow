<?php
/**
 * Fornece funções úteis para manipular a sessão
 *
 * @package
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class SessionManager
{
  /**
   * Retorna a sessão do usuário logado.
   * 
   * @return Zend_Session_Namespace
   */
  public static function getSession()
  {
    $context = &get_instance();    
    
    // instancia a sessão
    return new Zend_Session_Namespace($context->config->item('session_name'),true);
  }

  /**
   * Armazena os dados da requisição na sessão
   *
   * @return null
   */
  public static function setRequestData($data)
  {
    $context = &get_instance();

    $session = new Zend_Session_Namespace($context->config->item('session_name'),true);

    $session->request = $data;
  }

  /**
   * Função utilitária que retorna os dados da requisição armazenados na sessão.
   *
   * @param Mixed[string/null] $index
   * @return array
   */
  public static function getRequestData($index = null)
  {
    $context = &get_instance();

    $session = new Zend_Session_Namespace($context->config->item('session_name'),true);

    if (!$index)
      return $session->request;
    else
    {
      if (isset($session->request[$index]))
        return $session->request[$index];
      else
        return false;
    }
    
  }
}
?>
