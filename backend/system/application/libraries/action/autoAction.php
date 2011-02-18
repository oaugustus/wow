<?php
/**
 * autoAction
 *
 * This class has been auto-generated by the CI Framework
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id:
 */
class autoAction extends Controller
{
  /**
   * Overrides class construct.
   * 
   */
  public function __construct()
  {
    parent::__construct();

    // adds filter beheavior
    Doctrine::getTable('class_name')->addTemplate('Filterable', new Filterable());

    // adds extjs list behavior
    Doctrine::getTable('class_name')->addTemplate('ExtList', new ExtList());

    // adds link behavior
    Doctrine::getTable('class_name')->addTemplate('Link', new LinkTemplate());
  }
  
  /**
   * Lista os registros do modelo.
   *
   * @remotable
   *
   * @param array $request Parâmetros da requisição
   *
   * @return array Lista de registros encontrada
   */
  public function index(array $request)
  {
    // seta os dados da requisição no escopo da sessão
    SessionManager::setRequestData($request);

    return Doctrine::getTable('class_name')
                            ->applyFilter($this->autoSelections, $request);
  }

  /**
   * Recupera um registro de acordo com o seu id.
   *
   * @remotable
   *
   * @param array $request Lista de parâmetros da requisição
   *
   * @return array Registro localizado, caso contrário retorna uma lista vazia
   */
  public function show(array $request)
  {
    // seta os dados da requisição no escopo da sessão
    SessionManager::setRequestData($request);

    // recupera o registro, se for possível
    $record = Doctrine::getTable('class_name')->find($request['id']);

    // se o registro existir
    if ($record)
    {
      // retorna o registro encontrado
      return array(
        'results' => array(
         $record->toArray()
        ),
        'total' => $record->count()
      );
    }
    // se o registro não existe
    else
    {
      // define o resultado da operação como vazio
      return array(
        'results' => array(),
        'total' => 0
      );
    }

  }

  /**
   * Insere ou atualiza um registro.
   *
   * @remotable
   * @formHandler
   *
   * @param array $request Parâmetros da requisição
   *
   * @return boolean True em caso de êxito, caso contrário retorna exceção
   */
  public function save(array $request)
  {
    // seta os dados da requisição no escopo da sessão
    SessionManager::setRequestData($request);

    if (Security::hasPrivilege(strtolower($this->basePermission)."-create")
            || Security::hasPrivilege(strtolower($this->basePermission)."-edit"))
    {
      $rId = isset($request['id']) ? $request['id'] : 0;

      // se for atualização
      if ($rId > 0)
      {
          // recupera o registro pelo seu id
          $record = Doctrine::getTable('class_name')->find($request['id']);
      }
      else
      {
          // cria um novo objeto do registro
          $record = new class_name();

          if (isset($request['id']))
            unset($request['id']);
      }

      try
      {
        // chama o método presave
        $this->preSave($record);

        // une o registro com os dados recebidos da requisição
        $r = array_merge($record->toArray(),$request);

        // sincroniza o registro com o array
        $record->synchronizeWithArray($r);

        // salva o registro
        $record->save();

        // retorna true como resultado da operação
        return true;
      }
      catch(Exception $e)
      {
        // lança uma exceção
        throw new Exception('Falha ao salvar o registro: '.$e->getMessage()."\n".
          $e->getTraceAsString()
        );

      }
      
    }
    else
    {
        // lança uma exceção de acesso negado
        throw new Exception('Acesso negado: voc&ecirc; n&atilde;o tem permiss&atilde;o para executar esta a&ccedil;&atilde;o!');
    }
  }

  /**
   * Atualiza vários registros em lote.
   *
   * @remotable
   *
   * @param array $request Parâmetros da requisição
   *
   * @return boolean True em caso de êxito, caso contrário retorna exceção
   */
  public function saveBatch(array $request)
  {
    // seta os dados da requisição no escopo da sessão
    SessionManager::setRequestData($request);

    if (Security::hasPrivilege(strtolower($this->basePermission)."-save"))
    {
      $records = $request['records'];
      $exceptions = array();

      if (!is_array($records))
      {
          $records = array($records);
      }

      foreach($records as $rs)
      {
        $record = Doctrine::getTable('class_name')->find($rs['id']);

        try
        {
          // chama o método pré-saveBath
          $this->preSaveBatch($record);

          // sincroniza o registro com o array
          $record->synchronizeWithArray($rs);

          // salva o registro sincronizado
          $record->save();
        }
        catch(Exception $e)
        {
          // armazena as exceções em um array
          $exceptions[] = "Falha ao salvar o registro: ".$e->getMessage()."\n"
            .$e->getTraceAsString();
        }
      }

      // se não existir erros
      if (empty ($exceptions))
      {
        // retorna true
        return true;
      }
      else
      {
        // lança as exceções
        throw new Exception(implode("\n", $exceptions));
      }
      
    }
    else
    {
        // lança uma exceção de acesso negado
        throw new Exception('Acesso negado: voc&ecirc; n&atilde;o tem permiss&atilde;o para executar esta a&ccedil;&atilde;o!');
    }
  }

  /**
   * Remove um registro pelo seu ID.
   *
   * @remotable
   *
   * @param array $request Parâmetros da requisição
   *
   * @return array Lista vazia indicando que o registro foi removido
   */
  public function remove(array $request)
  {
    // seta os dados da requisição no escopo da sessão
    SessionManager::setRequestData($request);

    if (Security::hasPrivilege(strtolower($this->basePermission)."-remove"))
    {
      // recupera o registro, se for possível
      $record = Doctrine::getTable('class_name')->find($request['records']['id']);

      // se o registro existir
      if ($record)
      {
        try
        {
          // chama o método pre-remove
          $this->preRemove($record);

          // tenta remover o mesmo
          $record->delete();

          // retorna uma lista vazia indicando que o registro foi removido
          return array(
            'records' => array()
          );
        }
        catch(Exception $e)
        {
          // lança exceção
          throw new Exception("Falha ao remover o registro: ".$e->getMessage()."\n"
            .$e->getTraceAsString()
          );
        }
      }
      
    }
    else
    {
        // lança uma exceção de acesso negado
        throw new Exception('Acesso negado: voc&ecirc; n&atilde;o tem permiss&atilde;o para executar esta a&ccedil;&atilde;o!');
    }
  }

  /**
   * Empty method to preSave action
   */
  public function preSave(&$record){}

  /**
   * Empty method to preSaveBatch action
   */
  public function preSaveBatch(&$record){}

  /**
   * Empty method to preRemove action
   */
  public function preRemove(&$record){}

}