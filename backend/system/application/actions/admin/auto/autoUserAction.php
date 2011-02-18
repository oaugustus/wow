<?php
/**
 * autoUserAction
 *
 * This class has been auto-generated by the CI Framework
 *
 * @package    CodeIgniter
 * @subpackage libraries
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id:
 */
class autoUserAction extends Controller
{
  /**
   * Sobrecarrega o construtor da classe.
   */
  public function __construct()
  {
    parent::__construct();

    // adiciona o comportamento de filtro à classe
    Doctrine::getTable('User')->addTemplate('Filterable', new Filterable());
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
    return Doctrine::getTable('User')
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
    // recupera o registro, se for possível
    $record = Doctrine::getTable('User')->find($request['id']);

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
    // se for atualização
    if (isset($request['id']))
    {
        // recupera o registro pelo seu id
        $record = Doctrine::getTable('User')->find($request['id']);
    }
    else
    {
        // cria um novo objeto do registro
        $record = new User();
    }

    try
    {
      // sincroniza o registro com o array
      $record->synchronizeWithArray($request);

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
    $records = $request['records'];
    $exceptions = array();

    if (!is_array($records))
    {
        $records = array($records);
    }

    foreach($records as $rs)
    {
      $record = Doctrine::getTable('User')->find($rs['id']);

      try
      {
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
    // recupera o registro, se for possível
    $record = Doctrine::getTable('User')->find($request['records']['id']);

    // se o registro existir
    if ($record)
    {
      try
      {
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

}