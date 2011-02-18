<?php
/**
 * Classe que disponibiliza o método estático para a tarefa de carregamento de módulos.
 *
 * @package    CodeIgniter
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class Doctrine_CI_DropTask
{
    /**
     * Apaga uma aplicação.
     *
     * @param array $param
     */
    public static function dropApplication($param)
    {
      // recupera a instância da aplicação
      $app = self::getApplication($param['application']);

      // deleta a aplicação do banco de dados
      $app->delete();

      // deleta o arquivo de configuração da aplicação
      @unlink($param['config_path']."/generator/".$param['application'].".yml");
      
    }

    /**
     * Apaga um pacote.
     *
     * @param array $param
     */
    public static function dropPackage($param)
    {
      // recupera a instância da aplicação
      $app = self::getApplication($param['application']);
      $pack = self::getPackage($param['package']);

      // deleta a aplicação do banco de dados
      $pack->delete();

      // carrega a aplicação
      $application = sfYaml::load($param['config_path']."/generator/".$param['application'].".yml");

      foreach ($application['packages'] as $name => $package)
      {
        if (strtolower($name) == strtolower($param['package']))
        {
          unset($application['packages'][$name]);
        }
      }
      
      file_put_contents($param['config_path']."/generator/".$param['application'].".yml", sfYaml::dump($application,100));
    }

    /**
     * Apaga um pacote.
     *
     * @param array $param
     */
    public static function dropModule($param)
    {
      // recupera a instância da aplicação
      $app = self::getApplication($param['application']);
      $pack = self::getPackage($param['package']);
      $module = self::getModule($param['module']);

      // deleta a aplicação do banco de dados
      $module->delete();

      // carrega a aplicação
      $application = sfYaml::load($param['config_path']."/generator/".$param['application'].".yml");

      foreach ($application['packages'] as $name => $package)
      {
        foreach ($package['modules'] as $mName => $module)
        {
          if (strtolower($mName) == strtolower($param['module']))
          {
            unset($application['packages'][$name]['modules'][$mName]);
          }

        }
      }

      file_put_contents($param['config_path']."/generator/".$param['application'].".yml", sfYaml::dump($application,100));
    }

    /**
     * Pega a instância de uma aplicação pelo seu nome, caso não encontre,
     * cria uma nova instância.
     * 
     * @param string $name Nome da aplicação a ser recuperada
     *
     * @return Doctrine_Record Registro da aplicação
     */
    private static function getApplication($name)
    {
      // tenta localizar o registro da aplicação pelo seu nome
      $app = Doctrine_Core::getTable('Application')->findByName($name);

      // caso encontre
      if ($app->count() > 0)
      {
        // define a aplicação como o primeiro registro da lista
        $app = $app->getFirst();
      }
      else
      {
        // caso contrário, instância um novo objeto
        $app = new Application();
        $app->name = $name;
      }

      // retorna a instância da aplicação
      return $app;
    }

    /**
     * Pega a instância de um pacote, caso a instância não exista, cria uma
     * nova instância.
     * 
     * @param string $name Nome do pacote
     *
     * @return Doctrine_Record Registro do pacote
     */
    private static function getPackage($name)
    {
      // tenta localizar o registro do pacote pelo seu nome
      $pack = Doctrine_Core::getTable('Package')->findByName($name);

      // caso encontre
      if ($pack->count() > 0)
      {
        // define a aplicação como o primeiro registro da lista
        $pack = $pack->getFirst();
      }
      else
      {
        // caso contrário, instância um novo objeto
        $pack = new Package();
        $pack->name = $name;
      }

      // retorna a instância do pacote
      return $pack;
    }

    /**
     * Pega a instância de um módulo pelo seu nome, caso a instância não exista,
     * cria uma nova instância.
     *
     * @param string $name Nome do módulo
     *
     * @return Doctrine_Record Registro do módulo
     */
    private static function getModule($name)
    {
      // tenta localizar o registro do pacote pelo seu nome
      $module = Doctrine_Core::getTable('Module')->findByName($name);

      // caso encontre
      if ($module->count() > 0)
      {
        // define a aplicação como o primeiro registro da lista
        $module = $module->getFirst();
      }
      else
      {
        // caso contrário, instância um novo objeto
        $module = new Module();
        $module->name = $name;
      }

      // retorna a instância do pacote
      return $module;
    }

    /**
     * Decodifica os valores dos elementos de um array do formato utf8
     * 
     * @param array $array Array a ser decodificado
     * 
     * @return array Array decodificado do formato utf8
     */
    private static function fromUtf8($array)
    {
      $return = array();

      foreach ($array as $key => $value)
      {
        if (is_string($value))
        {
          $value = utf8_decode($value);
        }

        $return[$key] = $value;
      }

      return $return;
    }

}
