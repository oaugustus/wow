<?php
/**
 * ExtDCacheProvider
 *
 * Classe que gera a cache das funções remotas expostas pela API do ExtDirect.
 * Based on goachka, ExtJS forum member, code.
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id:
 */
class ExtDCacheProvider
{

  private $filePath = null;
  private $cache = false;

  /**
   * Construtor da classe.
   *
   * @param array $params Parâmetros de configuração da classe
   */
  public function __construct($params)
  {
    // se o path do arquivo de cache foi definido
    if(is_string($params['filePath']))
    {
      // armazena o filepath no atributo da classe, adicionando o path da aplicação
      $this->filePath = APPPATH.$params['filePath'];

      // se o arquivo não existir e não for possível criá-lo
      if(!file_exists(APPPATH.$params['filePath']) && !touch(APPPATH.$params['filePath']))
      {
        // lança exceção
        throw new Exception('Unable to create or access ' . $params['filePath']);
      }
    }

  }

  /**
   * Retorna a API dos métodos expostos que estão no arquivo de cache.
   * 
   * @return array API com a relação de métodos expostos.
   */
  public function getAPI()
  {
    $this->parse();
    
    return $this->cache;
  }

  /**
   * Verifica se a API gerada foi modificada.
   * 
   * @param array $apiInstance
   * @return boolean Retorna true se a API foi modificada, false caso contrário
   */
  public function isModified($apiInstance)
  {
    $ci = &get_instance();
    
    if(!$apiInstance instanceof ExtDAPI)
    {
      throw new Exception('You have to pass an instance of ExtDirect_API to isModified function');
    }

    if ($ci->config->item('debug_mode'))
    {
      return true;
    }
    else
    {
      return false;
    }

  }

  /**
   * Salva a API dos métodos expostos em um arquivo de cache.
   *
   * @param ExtDAPI $apiInstance Instância da API do ExtDirect
   */
  public function save($apiInstance)
  {
    if(!$apiInstance instanceof ExtDAPI)
    {
      throw new Exception('You have to pass an instance of ExtDirect_API to save function');
    }

    $cache = json_encode($apiInstance->getAPI());
    
    $this->write($cache);
  }

  /**
   * Recupera as modificações ocorridas na API em relação à armazenada em cache.
   * 
   * @param ExtDAPI $apiInstance
   * 
   * @return array Relação das modificações da API
   */
  private function getModifications($apiInstance)
  {
    if(!$apiInstance instanceof ExtDAPI)
    {
      throw new Exception('You have to pass an instance of ExtDirect_API to getModifications function');
    }

    $modifications = array();
    $classesPaths = $apiInstance->getClassesPaths();

    foreach($classesPaths as $path)
    {
      if(file_exists($path))
      {
        $modifications[$path] = filemtime($path);
      }
    }

    return $modifications;
  }

  /**
   * Escreve a API do ExtDirect no formato texto em um arquivo de cache.
   * 
   * @param string $content API(texto) a ser escrita no arquivo de cache.
   * @param boolean $append True se o conteúdo será anexado, false caso o mesmo deva sobrescrever o anterior.
   */
  function write($content = '', $append = false)
  {
    file_put_contents($this->filePath, $content, $append ? FILE_APPEND : 0);
  }


  /**
   * Parseia o conteúdo do arquivo de cache para um array PHP.
   * 
   * @return null
   */
  private function parse()
  {
    // se a cache ainda não existir
    if ($this->cache === false)
    {
      // recupera o conteúdo do arquivo de cache
      $content = file_get_contents($this->filePath);

      // se o conteúdo do arquivo estiver vazio
      if(strlen($content) === 0)
      {
        // define a cache com os valores vazios
        $this->cache =  array();

        // saí do método
        return;
      }

      // seta a cache com o conteúdo do arquivo de cache decodificado
      $this->cache = json_decode($content, true);
    }
  }
}
