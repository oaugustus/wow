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
    //LogManager::info('direct.__construct(): Início');
    
    // chama o construtor da classe base
    parent::__construct();

    // inicializa o objeto de sessão do Zend Framework
    Zend_Session::start();

    // configura o objeto de sessão
    Zend_Session::setOptions(array('strict' => true));

    //LogManager::info('direct.__construct(): Após inicialização da sessão');

    // adiciona a biblioteca de manipução da API de ExtDirect do CI
    $this->directAPI = new ExtDAPI();

    //LogManager::info('direct.__construct(): Após instanciar API');
    
    $this->directCache = new ExtDCacheProvider(
      array(
        'filePath' => 'cache/api_cache.json'
       )
    );

    //LogManager::info('direct.__construct(): Após definir o cache');
  }

  /**
   * Inicializa os parâmetros básicos de configuração da API
   */
  private function initAPI()
  {
    // remove a reportagem de erros não significativos
    error_reporting(~E_WARNING);

    // define a url do método de roteamento para a API do ExtDirect
    $this->directAPI->setRouterUrl($this->config->item('base_url').$this->config->item('index_page').'/direct/router'); // default

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
  }

  /**
   * Gera a API do ExtDirect.
   * 
   * @param boolean $output Define se a saída deverá ser impressa na tela
   */
  public function api($output = true)
  {
    // inicializa a api
    $this->initAPI();
    
    // se $output estiver setado 
    if($output)
    {
      // gera a definição da API para a interface
      $this->directAPI->output();
    }

    // registra o estado do ext-direct em sessão
    $session = new Zend_Session_Namespace('direct',true);
	$session->ext_direct_state = $this->directAPI->getState();

  }

  /**
   * Retorna a API para o ExtDesigner
   */
  public function getAPI()
  {
    // inicializa api
    $this->initAPI();

    // imprime a api para o ext designer
    $this->directAPI->outputDesigner();
  }
  
  /**
   * Efetua o roteamento das requisições.
   */
  public function router()
  {
    //echo "\n".date('h:i:s');
    
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

    // despacha a requisição para o roteador do ExtDirect
    $this->directRouter->dispatch();

    //LogManager::info('direct.router(): Após do despache ');

    // escreve a resposta da requisição
    $this->directRouter->getResponse(true);

    //echo "\n".date('h:i:s');
    //die();

    //LogManager::info('direct.router(): Após geração de resposta ');

  }
}