<?php
/**
 * ExtDAPI
 *
 * Classe que fornece os métodos para a manipulação da API do ExtDirect.
 * Based on goachka, ExtJS forum member, code.
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id:
 */
class ExtDAPI
{
  private $routerUrl = 'router.php';
  private $cacheProvider = null;
  private $defaults = array();
  private $classes = array();
  private $remoteAttribute = '@remotable';
  private $formAttribute = '@formHandler';
  private $nameAttribute = '@remoteName';
  private $namespace = false;
  private $type = 'remoting';
  private $parsedClasses = array();
  private $parsedAPI = array();
  private $descriptor = 'Ext.app.REMOTING_API';

  /**
   * Recupera o estado das configurações da API.
   * 
   * @return array Array com as opções de configuração e seus valores
   */
  public function getState()
  {
    return array(
      'routerUrl' => $this->getRouterUrl(),
      'defaults' => $this->getDefaults(),
      'classes' => $this->getClasses(),
      'remoteAttribute' => $this->getRemoteAttribute(),
      'formAttribute' => $this->getFormAttribute(),
      'nameAttribute' => $this->getNameAttribute(),
      'namespace' => $this->getNamespace(),
      'parsedAPI' => $this->parsedAPI,
      'descriptor' => $this->descriptor
    );
  }

  /**
   * Seta o estado das configurações da API.
   * 
   * @param array $state Estado a ser aplicado às configurações
   */
  public function setState($state)
  {
    
    foreach ($state as $property => $value)
    {
      if ($property != 'classes' && $property != 'parsedAPI')
      {
        $method = 'set'.ucfirst($property);
        $this->$method($value);
      }
    }
    if(isset($state['classes']))
    {
      $this->classes = $state['classes'];
    }
    
    if(isset($state['parsedAPI']))
    {
      $this->parsedAPI = $state['parsedAPI'];
    }


  }

  /**
   * Adiciona classes à API do ExtDirect.
   * 
   * @param array $classes  Classes que serão adicionadas à API
   * @param array $settings Configurações a serem aplicadas à API
   */
  public function add($classes = array(), $settings = array())
  {
    $settings = array_merge(
      array(
      'autoInclude' => false,
      'basePath' => '',
      'seperator' => '_',
      'prefix' => '',
      'subPath' => ''
      ),
      $this->defaults,
      $settings
    );

    if(is_string($classes))
    {
      $classes = array($classes);
    }

    foreach($classes as $name => $cSettings)
    {
      if(is_int($name))
      {
        $name = $cSettings;
        $cSettings = array();
      }
      
      $cSettings = array_merge($settings, $cSettings);
      $cSettings['fullPath'] = $this->getClassPath($name, $cSettings);
      $this->classes[$name] = $cSettings;
    }
  }

  /**
   * Registra as classes actions que possuirem métodos expostos.
   * 
   */
  public function registerActions()
  {
    $this->getActionsClasses(array(APPPATH.'actions/'));
  }

  /**
   * Recupera a lista das classes Action.
   * @param array $paths Paths onde serão buscadas as classes
   */
  public function getActionsClasses($paths)
  {
    foreach ($paths as $path)
    {
      $dir = new DirectoryIterator($path);

      $this->paths[] = $path;
      $h = array();

      foreach ($dir as $fileInfo)
      {
         // remove as actions auto geradas pelas tarefas
         if($fileInfo->isDir() && !$fileInfo->isDot() && $fileInfo->getFilename() != 'auto')
         {
           $h[] = $path.$fileInfo->getFilename()."/";
         }
         else
         {           
           if (!$fileInfo->isDot())
           {
             // adiciona a classe encontrada à lista de classes da API
             $this->add(array(
               current(explode('.',$fileInfo->getFilename()))
             ));
           }
         }
      }

      if (count($h) > 0)
      {
        $this->getActionsClasses($h);
      }
    }

  }

  /**
   * Retorna, ou imprime a API do ExtDirect(JavaScript) para os métodos expostos.
   * 
   * @param boolean $print Se a API deverá ser impressa, default (true)
   * 
   * @return string Resposta
   */
  public function output($print = true)
  {
    $saveInCache = false;
    
    if(isset($this->cacheProvider))
    {
      if(!$this->cacheProvider->isModified($this))
      {
        $api = $this->cacheProvider->getAPI();

        if($print === true)
        {
          $this->_print($api);
        }

        $this->parsedClasses = $this->classes;
        $this->parsedAPI = $api;
        
        return $api;
      }
      $saveInCache = true;
    }

    $api = $this->getAPI();

    if($saveInCache)
    {
      $this->cacheProvider->save($this);
    }

    if($print === true)
    {
      $this->_print($api);
    }
    
    return $api;
  }

  /**
   * Retorna, ou imprime a API do ExtDirect(JavaScript) para os métodos expostos
   * de forma exclusiva para o ExtDesigner.
   *
   * @return string Resposta
   */
  public function outputDesigner()
  {
    $saveInCache = false;

    if(isset($this->cacheProvider))
    {
      if(!$this->cacheProvider->isModified($this))
      {
        $api = $this->cacheProvider->getAPI();

        echo json_encode($api);

        $this->parsedClasses = $this->classes;
        $this->parsedAPI = $api;

        return true;
      }
      $saveInCache = true;
    }

    $api = $this->getAPI();

    if($saveInCache)
    {
      $this->cacheProvider->save($this);
    }

     echo json_encode($api);

     return true;
  }

  /**
   * Verifica se dois arrays são idênticos.
   * 
   * @param array $old
   * @param array $new
   * 
   * @return boolean True se os arrays são iguais, false caso contrário
   */
  public function isEqual($old, $new)
  {
    return serialize($old) === serialize($new);
  }

  /**
   * Retorna a API do ExtDirect, criando-a caso ela não esteja em cache.
   * 
   * @return array Definição da API do ExtDirect
   */
  public function getAPI()
  {
    // se não houve alteração em relação à cache
    if($this->isEqual($this->classes, $this->parsedClasses))
    {
      // retorna a cache
      return $this->getParsedAPI();
    }

    // cria um array para armazenar as classes para as quais será gerada a API
    $classes = array();

    // para cada classe action na lista de classes da classe
    foreach($this->classes as $class => $settings)
    {
      // Cria um array para armazenar os métodos da classe
      $methods = array();

      // here the reflection magic begins
      if(class_exists($settings['prefix'] . $class))
      {
        $methods = $this->getRemoteMethods($class, $settings);
        
        if(count($methods) > 0)
        {
          $class = str_replace('Action','',$class);
          $classes[$class] = $methods;
        }
      }
    }

    $api = array(
      'url' => $this->routerUrl,
      'type' => $this->type,
      'actions' => $classes,
      'descriptor' => $this->descriptor
    );

    if($this->namespace !== false)
    {
      $api['namespace'] = $this->namespace;
    }

    $this->parsedClasses = $this->classes;
    $this->parsedAPI = $api;

    return $api;
  }

  /**
   * Extrái os métodos expostos de uma classe.
   * 
   * @param string $class    Nome da classe
   * @param array  $settings Configurações da classe
   *
   * @return array Métodos expostos da classe
   */
  private function getRemoteMethods($class, $settings)
  {
    // variável para armazenar os métodos expostos
    $methods = array();

    // objeto de reflexão da classe
    $rClass = new ReflectionClass($settings['prefix'] . $class);

    // pega os métodos da classe
    $rMethods = $rClass->getMethods();

    // armazena os métodos expostos da classe
    foreach($rMethods as $rMethod)
    {
      // se o método for público e tiver bloco de documentação
      if( $rMethod->isPublic() && strlen($rMethod->getDocComment()) > 0)
      {
        // pega a documentação do método
        $doc = $rMethod->getDocComment();

        // pega a informação se o método está exposto
        $isRemote = !!preg_match('/' . $this->remoteAttribute . '/', $doc);

        // se o método estiver definido como exposto
        if($isRemote)
        {
          // armazena os dados do método em variável local
          $method = array(
            'name' => $rMethod->getName(),
            'len' => $rMethod->getNumberOfParameters(),
          );

          // define propriedades remotas do método
          if(!!preg_match('/' . $this->nameAttribute . ' ([\w]+)/', $doc, $matches))
          {
            $method['serverMethod'] = $method['name'];
            $method['name'] = $matches[1];
          }
          
          // define propriedade formHandler (o método é um manipulador de formulários)
          if(!!preg_match('/' . $this->formAttribute . '/', $doc))
          {
            $method['formHandler'] = true;
          }

          // armazena o método no array de métodos remotos
          $methods[] = $method;
        }
      }
    }

    // retorna os métodos expostos
    return $methods;    
  }

  /**
   * Recupera a API parseada em array.
   * 
   * @return array API do ExtDirect parseada em array
   */
  public function getParsedAPI()
  {
    return $this->parsedAPI;
  }

  /**
   * Pega o caminho completo da classe.
   * 
   * @param string $class
   * @param array $settings
   * @return String Caminho completo até uma classe
   */
  public function getClassPath($class, $settings = false)
  {
    if(!$settings)
    {
      $settings = $this->_settings;
    }

    if($settings['autoInclude'] === true)
    {
      $path = $settings['basePath'] . DIRECTORY_SEPARATOR .
        $settings['subPath'] . DIRECTORY_SEPARATOR .
        $class . '.php';
      $path = str_replace('\\\\', '\\', $path);
    } 
    else
    {
      $rClass = new ReflectionClass($settings['prefix'] . $class);
      $path = $rClass->getFileName();
    }

    return APPPATH.$path;
  }

  /**
   * Recupera o path das classes Actions que terão seus métodos expostos.
   *
   * @return array Paths para os arquivos das classes Actions.
   */
  public function getClassesPaths()
  {
    $classesPaths = array();
    
    foreach($this->getClasses() as $name => $settings)
    {
      $classesPaths[] = $this->getClassPath($name, $settings);
    }

    return $classesPaths;
  }

  public function _print($api)
  {
    header('Content-Type: text/javascript');

    echo ($this->namespace ?
    'Ext.ns(\'' . substr($this->descriptor, 0, strrpos($this->descriptor, '.')) . '\'); ' . $this->descriptor:
    'Ext.ns(\'Ext.app\'); ' . 'Ext.app.REMOTING_API'
    );
    echo ' = ';
    echo json_encode($api);
    echo ';';
    echo "Ext.Direct.addProvider(Ext.app.REMOTING_API);";

  }
  
  // Geters and  setters
  
  public function getClasses()
  {
    return $this->classes;
  }

  public function setRouterUrl($routerUrl = 'router.php')
  {
    if(isset($routerUrl))
    {
      $this->routerUrl = $routerUrl;
    }
  }

  public function getRouterUrl()
  {
    return $this->routerUrl;
  }

  public function setCacheProvider($cacheProvider)
  {
    if($cacheProvider instanceof ExtDCacheProvider)
    {
      $this->cacheProvider = $cacheProvider;
    }
  }

  public function getCacheProvider()
  {
    return $this->cacheProvider;
  }

  public function setRemoteAttribute($attribute)
  {
    if(is_string($attribute) && strlen($attribute) > 0)
    {
      $this->remoteAttribute = $attribute;
    }
  }

  public function getRemoteAttribute()
  {
    return $this->remoteAttribute;
  }

  public function setDescriptor($descriptor)
  {
    if(is_string($descriptor) && strlen($descriptor) > 0)
    {
      $this->descriptor = $descriptor;
    }
  }

  public function getDescriptor()
  {
    return $this->descriptor;
  }

  public function setFormAttribute($attribute)
  {
    if(is_string($attribute) && strlen($attribute) > 0)
    {
      $this->formAttribute = $attribute;
    }
  }

  public function getFormAttribute()
  {
    return $this->formAttribute;
  }

  public function setNameAttribute($attribute)
  {
    if(is_string($attribute) && strlen($attribute) > 0)
    {
      $this->nameAttribute = $attribute;
    }
  }

  public function getNameAttribute()
  {
    return $this->nameAttribute;
  }

  public function setNamespace($namespace)
  {
    if(is_string($namespace) && strlen($namespace) > 0)
    {
      $this->namespace = $namespace;
    }
  }

  public function getNamespace()
  {
    return $this->namespace;
  }

  public function setDefaults($defaults, $clear = false)
  {
    if($clear === true)
    {
      $this->clearDefaults();
    }

    if(is_array($defaults))
    {
      $this->defaults = array_merge($this->defaults, $defaults);
    }
  }

  public function getDefaults()
  {
    return $this->defaults;
  }

  public function clearDefaults()
  {
    $this->defaults = array();
  }
}