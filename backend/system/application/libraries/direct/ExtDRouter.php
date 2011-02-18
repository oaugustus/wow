<?php
require_once BASEPATH.'codeigniter/ErrorHandler'.EXT;

class BogusAction
{
  public $action;
  public $method;
  public $data;
  public $tid;
}

/**
 * ExtDRouter
 *
 * Classe que possibilita o roteamento das requisições aos métodos expostos
 * pela API do ExtDirect.
 * Based on goachka, ExtJS forum member, code.
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id:
 */
class ExtDRouter
{
  public $data = null;
  public $isForm = false;
  public $isUpload = false;

  private $response = null;

  /**
   * Construtor da classe, inicializa a requisição.
   * 
   * @param array $params Parâmetros de configuração da classe ExtDRouter
   */
  public function  __construct($params)
  {
    $this->ci = get_instance();

    $this->setAPI($params['api']);

    // inicializa a requisição
    $this->parseRequest();
  }

  /**
   * Seta a API do ExtDirect para a classe.
   * 
   * @param ExtDAPI $api API do ExtDirect
   */
  public function setAPI($api)
  {
    if(!($api instanceof ExtDAPI))
    {
      throw new Exception('setAPI expects an instance of ExtDirectapi');
    }

    $this->api = $api;
  }

  /**
   * Inicializa a requisição feita através da API exposta.
   * 
   */
  private function parseRequest()
  {
    // extrai os dados da requisição
    if(isset($GLOBALS['HTTP_RAW_POST_DATA']))
    {
      $this->data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
    }
    else
    if(isset($_POST['extAction']))
    { // form post
      $this->isForm = true;
      $this->isUpload = $_POST['extUpload'] == 'true';

      $data = new BogusAction();
      $data->action = $_POST['extAction'];
      $data->method = $_POST['extMethod'];
      $data->tid = $_POST['extTID'];
      $data->data = array($_POST, $_FILES);

      $this->data = $data;
    }
    else
    {
        die('Invalid request.');
    }

  }

  /**
   * Despacha a requisição.
   * 
   * @return string Resposta da requisição
   */
  public function dispatch()
  {
    // se a resposta já estiver definida
    if(isset($this->response))
    {
       // sái do método
       return;
    }

    $response = null;
    $output = '';

    // se mais de um método foi requisitado
    if(is_array($this->data))
    {
      // cria um array para as respostas aos métodos remotos
      $response = array();

      // para cada método remoto chamado
      foreach($this->data as $d)
      {
        // processa a requisição do método
        $response[] = $this->rpc($d);
      }
    }
    // caso contrário
    else
    {
      // chama o método remoto para o único método chamado
      $response = $this->rpc($this->data);
    }

    // se a saída deve ser codificada no padrão utf8
    if ($this->ci->config->item('output_encode'))
    {
      // efetua a codificação
      array_walk_recursive($response, array($this, 'encodeToUtf8'));
    }
    
    // se o método chamado é um manipulador de formulários e é do tipo upload de arquivo
    if($this->isForm && $this->isUpload)
    {
      // define a resposta para chamadas de manipuladores de formulários
      $json = json_encode($response);
      $json = preg_replace("/&quot;/", '\\&quot;', $json);

      $output .= '<html><body><textarea>';
      $output .= $json;
      $output .= '</textarea></body></html>';
    }
    else
    {
      // define a resposta para métodos comuns
      $output = json_encode($response);
    }

    // seta a resposta global do objeto
    $this->response = $output;

    // retorna a resposta obtida
    return $output;
  }

  /**
   * Executa a requisição de um método remoto.
   * 
   * @param BogusAction $cdata
   * 
   * @return array Resposta à chamada do método
   */
  public function rpc($cdata)
  {
    $cdata->action.= 'Action';

    // recupera a relação de classes expostas
    $classes = $this->api->getClasses();

    try
    {
      // se a classe requisitada não está exposta
      if(!isset($classes[$cdata->action]))
      {
        // lança excessão
        throw new Exception('Call to undefined class: ' . $cdata->action);
      }

      // define a classe e o método chamados, respectivamente
      $class = $cdata->action;
      $method = $cdata->method;

      // instancia a sessão
      $session = SessionManager::getSession();

      // if user is not logged and action request is not logon action
      if (!$session->isLogged)
      {
        if ($class != 'UserAction')
        {
          // exception
          throw new Exception ('You are not logged to request this action!');
        }
        else
        if ($method != 'requestLogon' && $method != 'checkSession')
        {
          // exception
          throw new Exception ('You are not logged to request this action!');
        }
      }            

      $cconf = $classes[$class];
      $mconf = null;

      // não entendi essa parte de código
      $parsedAPI = $this->api->getParsedAPI();
      
      if(!empty($parsedAPI) && isset($parsedAPI['actions'][$class]))
      {
        foreach($parsedAPI['actions'][$class] as $m)
        {
          if($m['name'] === $method)
          {
            $mconf = $m;
            $serverMethod = isset($m['serverMethod']) ? $m['serverMethod'] : $method;
          }
        }
      }
      else
      {
        // pega os dados do método chamado remotamente
        $calledMethod = $this->getCalledMethod($class, $cconf, $method);
        $mconf = $calledMethod['method_conf'];
        $serverMethod = $calledMethod['server_method'];
      }

      // lança excessão caso o método chamado não esteja disponível
      if(!isset($mconf))
      {
        throw new Exception("Call to undefined or unallowed method: $method on class $class");
      }

      // lança excessão caso a requisição seja para um formulário e método não esteja definido para isso
      if($this->isForm && (!isset($mconf['formHandler']) || $mconf['formHandler'] !== true))
      {
        throw new Exception("Called method $method on class $class is not a form handler");
      }

      // seta os parâmetros do método a ser chamado
      $params = isset($cdata->data) && is_array($cdata->data) ? $cdata->data : array();

      // força a conversão dos parâmetros para array
      array_walk_recursive($params, array($this, 'convertToArray'));

      // se o número de parâmetros estiver incorreto
      if(count($params) < $mconf['len'])
      {
        // lança exceção
        throw new Exception("Not enough required params specified for method: $method on class $class");
      }

      // armazena os parâmetros em sessão
      $this->storeParams($params);

      // seta o objeto response inicial com os parâmetros requisitados
      $response = array(
        'type' => 'rpc',
        'tid' => $cdata->tid,
        'action' => str_replace('Action', '', $class),
        'method' => $method
      );

      // instancia a classe
      $className = $cconf['prefix'] . $class;
      $instance = new $className();

      // armazena os dados da requisição no escopo da sessão
      SessionManager::setRequestData($params[0]);
      
      // chama o método remoto passando os parâmetros da requisição, e armazena o resultado da chamada
      $response['result'] = call_user_func_array(array($instance, $serverMethod), $params);
    }    
    // caso qualquer exceção seja lançada, retorna objeto de erro
    catch(Exception $e)
    {
      // cria o log da exceção
      //$exception = $this->createExceptionLog($e, $cdata);
      
      $response = array(
        'type' => 'exception',
        'tid' => $cdata->tid,
        'message' => "Exception: ".$e->getMessage(),
        'where' => ""//$e->getTraceAsString()
      );
    }

    // retorna o array de resposta
    return $response;
  }


  /**
   * Cria o registro da exceção.
   * 
   * @param Exception $e
   * @param array     $request
   */
  private function createExceptionLog($e, $request)
  {
    $session = SessionManager::getSession();
    
    $exception = new ExceptionLog();
    $exception->user_id = $session->login['id'];
    $exception->exception = $e->getMessage();
    $exception->dt_exception = date("Y-m-d H:i:s");
    $exception->trace = $e->getTraceAsString();
    $exception->request = JsonUtil::format(json_encode($request));

    $exception->save();

    return $exception;
  }

  /**
   * Armazena os parâmetros da requisição em sessão, se a sessão existir
   * 
   * @param array $params
   */
  private function storeParams($params)
  {
    // recupera a instância do contexto de execução do CI
    $context = &get_instance();

    // instancia a sessão
    $session = new Zend_Session_Namespace($context->config->item('session_name'));

    // se o usuário estiver autenticado
    if ($session->isLogged)
    {
      $session->params = $params;

      if (isset($params[1]))
        $session->files = $params[1];
    }
    
  }

  /**
   * Retorna o método remoto chamado, se ele exitir ou estiver definido.
   *
   * @param  string $class  Nome da classe chamada
   * @param  array  $cconf  Configurações definidas para a classe
   * @param  string $method Nome do método chamado
   * 
   * @return array  Dados do método remoto chamado
   */
  private function getCalledMethod($class, $cconf, $method)
  {
    // gera a classe de reflexão para a classe chamada
    $rClass = new ReflectionClass($cconf['prefix'] . $class);

    // trata métodos remotos com nomes diferentes dos nomes definidos
    if(!$rClass->hasMethod($method))
    {
      // gera reflexão para os métodos
      $rMethods = $rClass->getMethods();

      // procura o método chamado pelo atributo remoteAttribute
      foreach($rMethods as $rMethod)
      {
        $doc = $rMethod->getDocComment();
        if($rMethod->isPublic() &&strlen($doc) > 0 &&
          !!preg_match('/' . $this->_remoteAttribute . '/', $doc) &&
          !!preg_match('/' . $this->_nameAttribute . ' ([w]+)/', $doc, $matches) &&
          $method === $matches[1]
        ) {
          $serverMethod = $rMethod->getName();
          $mconf = array(
            'name' => $method,
            'len' => $rMethod->getNumberOfRequiredParameters(),
          );
          if(!!preg_match('/' . $this->api->getFormAttribute() . '/', $doc))
          {
            $mconf['formHandler'] = true;
          }
        }
      }
      if(!$serverMethod)
      {
        throw new Exception("Call to undefined method: $method on class $class");
      }
    }
    // se o método existe na classe
    else
    {
      $rMethod = $rClass->getMethod($method);
      $doc = $rMethod->getDocComment();
      if($rMethod->isPublic() && strlen($doc) > 0)
      {
        if(!!preg_match('/' . $this->api->getRemoteAttribute() . '/', $doc))
        {
          $serverMethod = $method;
          $mconf = array(
            'name' => $method,
            'len' => $rMethod->getNumberOfRequiredParameters(),
          );
          if(!!preg_match('/' . $this->api->getFormAttribute() . '/', $doc))
          {
            $mconf['formHandler'] = true;
          }
        }
      }
    }

    // retorna os dados do método chamado
    return array(
      'server_method' => $serverMethod,
      'method_conf' => $mconf
    );
  }

  /**
   * Retorna a resposta para a requisição.
   *
   * @param boolean $print Se a resposta deverá ser impressa ou não
   * @return string
   */
  public function getResponse($print = false)
  {
    // se não houver resposta, trata a requisição
    if(!$this->response)
    {
      $this->dispatch();
    }

    // se a resposta deve ser impressa
    if($print !== false) 
    {
      // chama método pata a impressão da resposta
      $this->_print($this->response);
    }

    // retorna a resposta da requisição
    return $this->response;
  }

  /**
   * Imprime a resposta no navegador.
   * 
   * @param string $response Resposta a ser retornada para o cliente
   */
  public function _print($response)
  {
    if(!$this->isForm)
    {
      header('Content-Type: text/javascript');
    }   
    echo $response;
  }
  /**
   * Converte um valor, caso ele seja um objeto para um array.
   *
   * @param Mixed $value {Object/String/Integer/Array} valor a ser convertido
   * @param Mixed $key   {String/Integer} chave do elemento do array
   */
  private function convertToArray(&$value, &$key)
  {
    if (is_string($value)){
      // converte strings json para array
      $json = json_decode($value,true);
      if ($json){
        $value = $json;
      }      
    }

    // se o valor for textual e a configuração input_decode estiver definida com true
    if (is_string($value) && $this->ci->config->item('input_decode'))
    {
      $value = utf8_decode($value);
    }

    // se o valor é um objeto, converte para array
    if (is_object($value))
    {
      $value = (array)$value;
    }

    // chama a função recursive para forçar a conversão dos elementos do novo array
    if (is_array($value))
    {
      array_walk_recursive($value, array($this, 'convertToArray'));
    }
  }

  /**
   * Codifica os valores de um  array no formato utf8
   *
   * @param Mixed $value {Object/String/Integer/Array} valor a ser convertido
   * @param Mixed $key   {String/Integer} chave do elemento do array
   */
  private function encodeToUtf8(&$value, &$key)
  {
    // se o valor for textual e a configuração input_decode estiver definida com true
    if (is_string($value))
    {
      $value = utf8_encode($value);
    }
    // chama a função recursive para forçar a conversão dos elementos do novo array
    if (is_array($value))
    {
      array_walk_recursive($value, array($this, 'utf8Encode'));
    }
  }

}