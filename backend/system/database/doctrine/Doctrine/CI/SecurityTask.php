<?php
/**
 * Classe que disponibiliza os métodos estáticos para a execução das tarefas
 * relacionadas ao controle de acesso e permissões.
 *
 * @package    CodeIgniter
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class Doctrine_CI_SecurityTask
{

    /**
     * Array que armazena as permissões existens no arquivo de configuração.
     * 
     * @var array
     */
    private static $inConfig = array();

    /**
     * Array que armazena as permissões existens no banco de dados.
     * 
     * @var array
     */
    private static $inDB = array();

    /**
     * Array que armazena a estrutura de permissões que será gravada no arquivo.
     * 
     * @var array
     */
    private static $schema;

    /**
     * Array que armazena as configurações default de uma permissão básica
     * no arquivo de configuração.
     *
     * @var array
     */
    private static $base = array('name' => '', 'description' => '', 'children' => null);
    
    /**
     * Carrega as permissões na base de dados a partir do arquivo de permissões
     * YAML no diretório de configuração.
     * 
     * @param array $param
     */
    public static function load($param)
    {
        // pega o arquivo de permissões
        $permission = sfYaml::load($param['config_path']."/security/permissions.yml");

        // pega o arquivo de configurações default para as permissões
        $defaults = sfYaml::load($param['config_path']."/security/default.yml");

        // se o arquivo foi encontrato, e foi convertido para array
        if (!is_array($permission))
        {
          $permission = array();          
        }

        // seta o array inicial com as configurações de permissão para definir
        // as permissões removidas
        foreach ($permission as $app => $packages)
        {
          foreach ($packages as $pack => $modules)
          {
            foreach ($modules as $module => $p)
            {
              self::setInConfig($p);

            }
          }
        }

        // seta as permissões presentes no banco
        self::setInDB();

        // remove as configurações que não existem mais no arquivo de configuração
        self::removePermissions();
        
        // chama o método para efetuar o carregamento das permissões no banco
        self::loadPermissions($permission, $defaults, $param);

        // reconstrói o arquivo de configurações
        self::rebuildYaml($param);
    }

    /**
     * Define o array inicial com todas as configurações que serão utilizadas
     * para identificar as permissões que foram removidas do arquivo de
     * configuração.
     * 
     */
    private static function setInConfig($cfg)
    {
      for ($i = 0; $i < count($cfg); $i++)
      {
        self::$inConfig[] = $cfg[$i]['name'];
        
        if (isset($cfg[$i]['children']))
        {
          self::setInConfig($cfg[$i]['children']);
        }
      }
    }

    /**
     * Seta as permissões armazenadas no banco de dados
     */
    private static function setInDB()
    {
      // recupera a lista de permissões no banco 
      $q = Doctrine_Query::create()
           ->select('p.name')
           ->from('Permission p');
      $rs = $q->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

      // armazena o nome de cada permissão na propriedade da classe
      foreach ($rs as $rec)
      {
        self::$inDB[] = $rec['name'];
      }
    }

    /**
     * Remove as permissões que não estão no arquivo de configuração
     * do banco de dados.
     * 
     * @param array $list Lista das configurações que serão removidas
     */
    private static function removePermissions()
    {
      // se existir alguma permissão no banco
      if (count(self::$inDB) > 0)
      {
        $list = array_diff(self::$inDB, self::$inConfig);
        
        foreach ($list as $name)
        {
          // recupera a instância da permissão e o seu nó na árvore de permissões
          $pInstance = self::getPermissionByName($name);
          $node = $pInstance->getNode();

          // remove o nó, se ele existir
          if ($node)
          {
            // deleta o nó
            $node->delete();
          }

          // deleta a permissão
          $pInstance->delete();
        }

      }
    }

    /**
     * Garante privilégio de acesso a todas as permissões existentes,
     * para um determinado grupo de usuários recebido como parâmetro.
     * 
     * @param array $param
     */
    public static function grantFullAccess($param)
    {
      // pega o grupo pelo seu id ou nome
      if (is_numeric($param['group']))
      {
          // pelo id
          $g = Doctrine_Core::getTable('Group')->find($param['group']);
      }
      else
      {
        // pelo nome
        $g = Doctrine_Core::getTable('Group')->findOneByName($param['group']);
      }

      // Remove todos os privilégios do grupo
      $g->Privilege->delete();

      // pega a relação de todas as permissões
      $permissions = Doctrine_Core::getTable('Permission')->findAll();
      $priv = array();

      // cria a lista de privilégios para todas as permissões
      foreach ($permissions as $p)
      {
        $priv[] = array('group_id' => $g->id, 'permission_id' => $p->id);
      }

      // salva os privilégios do grupo
      $g->Privilege->fromArray($priv);
      $g->Privilege->save();      

    }

    /**
     * Carrega, efetivamente, as permissões setadas no arquivo de configuração
     * no banco de dados.
     * 
     * @param array $cfg     Configurações do arquivo permission.yaml
     * @param array $default Configurações default a serem aplicadas a cada permissão
     * @param array $param   Parâmetros de configuração da tarefa
     */
    private static function loadPermissions($cfg, $default, $param)
    {
      // recupera todos os módulos armazenado no banco de dados
      $modules = self::getModules();
      $appExist = false;
      $packExist = false;
      
      // percorre a lista de aplicações 
      foreach ($modules as $app)
      {
        // se a configuração estiver definida para a aplicação
        if (isset($cfg[$app['name']]))
        {
          // informa que a permissão existe para a aplicação
          $appExist = true;
        }
        
        // percorre a lista de pacotes da aplicação
        foreach ($app['Package'] as $pack)
        {
          // se a configuração estiver definida para o pacote
          if ($appExist & isset($cfg[$app['name']][$pack['name']]))
          {
            $packExist = true;
          }

          // percorre a lista de módulos do pacote
          foreach ($pack['Module'] as $module)
          {
            // se existe uma configuração definida para o módulo no arquivo yaml
            if ($packExist & isset($cfg[$app['name']][$pack['name']][$module['name']]))
            {
              // salva uma permissão que existe no arquivo de configurações
              self::saveExistent($cfg[$app['name']][$pack['name']][$module['name']], $module);
            }
            // se não existe configuração definida para o módulo no arquivo yaml
            else
            {
              // aplica a configuração default
              self::saveEmpty($default['Default'],$module);
            }
          }// final do foreach para o módulo
          
          $packExist = false;
        }// final do foreach para o pack

        $appExist = false;
      }// final do foreach para a app
      
    }

    /**
     * Salva as permissões do módulo que já estão definidas no arquivo de configuração
     * yaml.
     *
     * @param array           $cfg      Configurações definidas no arquivo de permissões
     * @param array           $module   Módulo para o qual serão definidas as permissões
     * @param Doctrine_Record $root     Raiz do grupo de permissões especificado em $cfg
     */
    private static function saveExistent($cfg, $module, $root = null)
    {
      // percorre a lista de permissões definidas no arquivo
      for ($i = 0; $i < count($cfg); $i++)
      {
        $p = $cfg[$i];

        // pega a instância da permissão
        $pInstance = self::getPermissionByName($p['name']);
        
        // se é uma nova instância da permissão
        if ($pInstance->id <= 0)
        {
          // define o vínculo com o módulo
          $pInstance->module_id = $module['id'];
        }
        
        $pAsArray = array_merge($pInstance->toArray(), $p);
        $pAsArray['description'] = utf8_decode($pAsArray['description']);// fix acentos
        
        $pInstance->synchronizeWithArray($pAsArray);
        $pInstance->save();
        
        // tira a permissão da lista de permissões removidas
        //unset(self::$removed[$p['name']]);

        // se não existe raiz para a permissão atual
        if (!$root)
        {
          // define a permissão como sendo uma raiz da árvore
          $tree = Doctrine_Core::getTable('Permission')->getTree();
          $tree->createRoot($pInstance);          
        }
        // se uma raiz foi especificada para a permissão
        else
        {
          // insere o registro do nó para a instância
          $pInstance->getNode()->insertAsLastChildOf($root);
        }

        // se a permissão possui subníveis
        if ($p['children'])
        {
          // chama o método recursivo para salvar os nós filhos
          self::saveExistent($p['children'], $module, $pInstance);
        }
        
        //print_r($pAsArray);
      }
    }

    /**
     * Salva o registro de permissões default para módulos não localizados
     * no arquivo de configuração.
     *
     * @param array $p       Array de permissões default
     * @param array $module  Módulo para o qual serão definidas as configurações default
     */
    private static function saveEmpty($p, $module)
    {
      // define a permissão como sendo uma raiz da árvore
      $tree = Doctrine_Core::getTable('Permission')->getTree();

      for ($i = 0; $i < count($p); $i++)
      {
        // recupera o nome da permissão
        $pName = str_replace('default', strtolower($module['name']), $p[$i]['name']);
        $pDescription = str_replace('default', strtolower($module['title']), $p[$i]['description']);

        // recupera uma instância, existente ou nova da permissão e seta
        // as suas propriedades
        $pInstance = self::getPermissionByName($pName);
        $pInstance->description = ($pDescription);
        $pInstance->module_id = $module['id'];

        // salva o registro da permissão
        $pInstance->save();

        $tree->createRoot($pInstance);
      }
      
    }

    /**
     * Recupera a coleção de Módulos armazenados no banco, organizados dentro de
     * pacotes e aplicações.
     *
     * @return array Lista de módulos cadastrados no banco de dados
     */
    private static function getModules()
    {
      // cria a query
      $q = Doctrine_Query::create()
          ->select('a.name, p.name, m.name, m.title')
          ->from('Application a, a.Package p, p.Module m');

      // executa a query e recupera os resultados como array
      $rs = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

      // retorna o resultado da consulta
      return $rs;
    }

    /**
     * Localiza e retorna uma permissão pelo seu nome único.
     * 
     * @param string $name Nome da permissão a ser localizada
     *
     * @return Doctrine_Record
     */
    private static function getPermissionByName($name)
    {
      $r = Doctrine_Core::getTable('Permission')->findOneByname($name);

      // se o registro não for localizado
      if (!$r)
      {
        // instância um novo registro
        $r = new Permission();
        $r->name = $name;
      }
      
      // localiza  e retorna o registro encontrado
      return $r;
    }

    /**
     * Reconstrói o arquivo de configurações de permissões a partir das
     * configurações armazenadas no banco de dados.
     *
     * @param array $param Parâmetros da configuração da tarefa
     */
    private static function rebuildYaml($param)
    {
      $modules = self::getModules();
      
      $treeObject = Doctrine_Core::getTable('Permission')->getTree();

      // percorre a lista de aplicações
      foreach ($modules as $app)
      {
        // percorre a lista de pacotes da aplicação
        foreach ($app['Package'] as $pack)
        {
          // percorre a lista de módulos do pacote
          foreach ($pack['Module'] as $module)
          {
            $q = Doctrine_Query::create()
                 ->select('p.name, p.id, p.description')
                 ->from('Permission p')
                 ->where('p.module_id=?',array($module['id']))
                 ->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY_HIERARCHY);
            
            $treeObject = Doctrine_Core::getTable('Permission')->getTree();
            $treeObject->setBaseQuery($q);
            $p = $treeObject->fetchTree();

            foreach ($p as $root)
            {
              if (count($root['__children']) > 0)
              {
                $root['children'] = self::setPermission($root['__children'], $app['name'], $pack['name'], $module['name']);

              }
              
              self::$schema[$app['name']][$pack['name']][$module['name']][] = array_intersect_key($root, self::$base);
            }
            
          }                    

        }
      }

      // grava as permissões no arquivo de configurações
      file_put_contents($param['config_path']."/security/permissions.yml", utf8_encode(sfYaml::dump(self::$schema,1000)));
    }

    /**
     * Seta as permissões filhas de uma raiz para ser gravada no arquivo de
     * configurações.
     * 
     * @param array  $root
     * @param string $app
     * @param string $pack
     * @param string $module
     */
    private static function setPermission($root, $app, $pack, $module)
    {
      $return = array();

      foreach ($root as $child)
      {
        
        if (count($child['__children']) > 0)
        {
          $child['children'] = self::setPermission($child['__children'], $app, $pack, $module);          
        }

        $return[] = array_intersect_key($child, self::$base);
      }

      return $return;
    }
}
