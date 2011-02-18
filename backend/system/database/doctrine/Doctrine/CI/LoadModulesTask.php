<?php
/**
 * Classe que disponibiliza o método estático para a tarefa de carregamento de módulos.
 *
 * @package    CodeIgniter
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class Doctrine_CI_LoadModulesTask
{
    public static function load($param)
    {
        $application = sfYaml::load($param['config_path']."/generator/".$param['application'].".yml");
        
        // se o arquivo foi encontrato, e foi convertido para array
        if (is_array($application))
        {
          self::loadModules($application, $param);
        }
        else
        {
          throw new Exception("Error: File ".$param['application'].".yml was not found in config directory. Modules was not loaded!");
        }
      
    }

    private static function loadModules($application, $param)
    {
      // recupera a instância da aplicação
      $app = self::getApplication($param['application']);
      $app->fromArray($application);
      $appConfig = self::fromUtf8($app->toArray());

      // cria variável para armazenar os pacotes
      $packages = array();
      
      foreach ($application['packages'] as $name => $config)
      {
        // recupera a instância do pacote
        $package = self::getPackage($name);
        $package->fromArray($config);       
        $package = self::fromUtf8($package->toArray());

        // se o id da aplicação não existe
        if (!$package['application_id'])
        {
          unset($package['application_id']);
        }
        
        foreach ($config['modules'] as $moduleName => $moduleCfg)
        {
          $module = self::getModule($moduleName);
          $module->fromArray($moduleCfg);
          $module = self::fromUtf8($module->toArray());
          
          // se o id da aplicação não existe
          if ($module['package_id'] <= 0)
          {
            unset($module['package_id']);
          }
          
          $package['Module'][] = $module;
        }
        
        // armazena o pacote instânciado na lista de pacotes
        $packages[] = $package;
      }

      $appConfig['Package'] = $packages;

      
      if ($app->id > 0)
      {
        $app->synchronizeWithArray($appConfig);
      }
      else
      {
        $app->fromArray($appConfig);
      }

      $app->save();
      
      //print_r($app->toArray());
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
