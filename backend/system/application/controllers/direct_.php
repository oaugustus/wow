<?php
/**
 * Direct
 *
 * Front-controller do CI, fornece ponto de acesso para os métodos remotos.
 *
 * @package    CodeIgniter
 * @subpackage controllers
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id:
 */
class Direct extends Controller
{
  /**
   * Construtor da classe.
   *
   * Instancia as bibliotecas padrões da classe.
   */
  public function __construct()
  {
    // chama o construtor da classe base
    parent::__construct();

    // inicializa o objeto de sessão do Zend Framework
    Zend_Session::start();

    // configura o objeto de sessão
    Zend_Session::setOptions(array('strict' => true));

    // adiciona a biblioteca de manipução da API de ExtDirect do CI
    $this->directAPI = new ExtDAPI();
    
    $this->directCache = new ExtDCacheProvider(
      array(
        'filePath' => 'cache/api_cache.json'
       )
    );
  }

  /**
   * Gera a API do ExtDirect.
   * 
   * @param boolean $output Define se a saída deverá ser impressa na tela
   */
  public function api($output = true)
  {
    // remove a reportagem de erros não significativos
    error_reporting(~E_WARNING);
    
    // define a url do método de roteamento para a API do ExtDirect
    $this->directAPI->setRouterUrl('http://localhost/sisman/backend/index.php/direct/router'); // default

    // define o provedor da cache dos métodos
    $this->directAPI->setCacheProvider($this->directCache);

    // define o namespace da API do Ext Direct
    $this->directAPI->setNamespace('Ext.app');

    // define o nome da variável da API do ExtDirect 
    $this->directAPI->setDescriptor('Ext.app.REMOTING_API');

    // define as propriedades default da API
    $this->directAPI->setDefaults(array(
      'autoInclude' => true,
      'basePath' => 'actions' // define o diretório onde estão as classes
                              // que terão seus métodos expostos.
    ));

    // Autoregistra as classes Action do CI
    $this->directAPI->registerActions();

    // se $output estiver setado 
    if($output)
    {
      // gera a definição da API para a interface
      $this->directAPI->output();
    }

    // registra o estado do ext-direct em sessão
    $session = new Zend_Session_Namespace('direct',true);
	$session->ext_direct_state = $this->directAPI->getState();

    /*
    // registra o estado do ext-direct em sessão
    $this->session->set_userdata(array(
      'ext-direct-state' => $this->directAPI->getState()
    ));*/
  }

  /**
   * Efetua o roteamento das requisições.
   */
  public function router()
  {
    $session = new Zend_Session_Namespace('direct',true);

    // se o estado do ext-direct não estiver definido
    if(!$session->ext_direct_state)
    {
      // gera o estado do ext-direct
      $this->api(false);
    }
    else
    {
      // seta o estado da API com os dados armazenados em sessão
      $this->directAPI->setState($session->ext_direct_state);

    }

    // carrega a classe de roteamento
    $this->directRouter = new ExtDRouter(array(
      'api' => $this->directAPI
    ));
    
    //$this->load->library('extdrouter', array('api' => $this->extdapi));

    // despacha a requisição para o roteador do ExtDirect
    $this->directRouter->dispatch();

    // escreve a resposta da requisição
    $this->directRouter->getResponse(true);
  }
}