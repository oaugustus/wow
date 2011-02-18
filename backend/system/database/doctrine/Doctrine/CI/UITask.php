<?php
/**
 * Classe que disponibiliza o método estático para a tarefa de criação de UI.
 *
 * @package    CodeIgniter
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class Doctrine_CI_UITask
{
    public static function generateUI($param)
    {
        $generator = sfYaml::load($param['config_path']."/generator/".$param['application'].".yml");
        $default   = sfYaml::load($param['config_path']."/generator/default.yml");
        
        // se o arquivo foi encontrato, e foi convertido para array
        if (is_array($generator))
        {
          self::buildUI($generator['packages'], $default, $param);
        }
        else
        {
          throw new Exception("Error: File generator.yml was not found in config directory. UI was not generated!");
        }
      
    }

    /**
     * Gera a UI ExtJS para as classes definidas no arquivo generator.yml.
     * 
     * @param array $generator Hash de configuração das UI para cada classe UI
     * @param array $default   Hash default de configuração das UI para cada classe UI
     * @param array $param     Parâmetros de configuração da tarefa
     */
    private static function buildUI($generator, $default, $param)
    {
      $default = self::glueProperties($default);

      foreach ($generator as $package => $module)
      {
        foreach ($module['modules'] as $class => $config)
        {
          $config  = self::glueProperties($config);
          $config['path'] = strtolower($param['application']);
          self::writeJsFiles($class, $config, $default, $param);
        }                
      }
    }

    /**
     * Pega as propriedades que possuem underscore e as une em um único nome.
     *
     * @param array $config Configurações  do componente
     * 
     * @return array
     */
    private static function glueProperties($config)
    {
      $nConfig = array();
      
      foreach ($config as $key => $value)
      {
        $parts = explode("_",$key);
        $key = $parts[0];

        for ($i = 1; $i < count($parts); $i++)
        {
          $part = $parts[$i];
          $key.= ucfirst($part);
        }

        if (is_array($value))
        {
          $value = self::glueProperties($value);
        }

        $nConfig[$key] = $value;
      }

      // retorna o novo array
      return $nConfig;
    }
    
    /**
     * Escreve os arquivos JavaScript da interface de usuário.
     *
     * @param string $class     Configurações da classe de UI
     * @param array  $component Configurações da classe de UI
     * @param array  $default   Configurações default da classe de UI
     * @param array  $param     Parâmetros de configuração da tarefa
     */
    private static function writeJsFiles($class, $component, $default, $param)
    {
      $appPath  = $param['app_path'];
      $webPath  = $param['web_path'];
      $uiPath   = $param['ui_path'];
      $autoPath = $param['ui_path'];
      
      // se o path do componente estiver definido
      if (isset($component['path']))
      {
        // se o mesmo ainda não existir
        if (!file_exists($uiPath."/".$component['path']))
        {
          // cria esse diretório
          mkdir($uiPath."/".$component['path']);
        }

        // redefine o path de onde serão criados os arquivos js
        $uiPath .= "/".$component['path'];
        
        // redefine o path de onde serão criados os arquivos auto-generados
        $autoPath .= "/".$component['path']."/auto";
      }
      else
      {
        $autoPath .= "/auto";
      }

      // se o diretório não exisir
      if (!file_exists($autoPath))
      {
        // cria esse diretório
        mkdir($autoPath);
      }

      file_put_contents($autoPath."/".$class."ModuleUI.js", self::getAutoModuleUI($class, $component, $default));

      if (!file_exists($uiPath."/".$class."Module.js"))
      {
        file_put_contents($uiPath."/".$class."Module.js", self::getModule($class));
      }      
      
    }

    /**
     * Retorna a definição do objeto do módulo.
     *
     * @param string $class     Nome da classe que será gerada
     * @param array  $component Configurações do componente
     * @param array  $default   Configurações default do componente
     *
     * @return string Especificação do módulo
     */
    private static function getAutoModuleUI($class, $component, $default)
    {
      $moduleClass = isset($component['moduleClass']) ? $component['moduleClass'] : 'Wow.Module';
      
      $content = "/**\n";
      $content.= " * {$class}ModuleUI.js\n *\n";
      $content.= " * Este arquivo foi gerado automaticamente. Não faça alterções neste arquivo.\n";
      $content.= " * @date ".date("Y-m-d H:i:s")." \n";
      $content.= " */\n";

      $content.= $class."ModuleUI = Ext.extend($moduleClass,{\n";
      
      if (isset($component['path']))
      {
        // remove a configuração do objeto
        unset($component['path']);
      }

      // aplica as configurações default
      $component = self::array_merge_recursive_distinct($default['Default'],$component);

      // codifica o componente em um array e formata o json gerado
      $config = JsonUtil::format(json_encode($component));

      $content.= "initComponent : function(){\n";
      $content.= "  Ext.apply(this,".$config.");\n";
      $content.= "    {$class}ModuleUI.superclass.initComponent.apply(this, arguments);\n";
      $content.= "}\n";

      $content.= "});";

      return $content;
    }

    /**
     * Retorna a definição do objeto do módulo.
     *
     * @param string $class     Nome da classe que será gerada
     *
     * @return string Especificação do módulo
     */
    private static function getModule($class)
    {
      $content = "/**\n";
      $content.= " * {$class}Module.js\n *\n";
      $content.= " * Este arquivo foi gerado automaticamente. Altere-o conforme necessário.\n";
      $content.= " * @date ".date("Y-m-d H:i:s")." \n";
      $content.= " */\n";
      
      $content.= $class."Module = Ext.extend(".$class."ModuleUI,{\n";
      $content.= "  initComponent : function(){\n";
      $content.= "    {$class}Module.superclass.initComponent.apply(this, arguments);\n";
      $content.= "  }\n";
      $content.= "});\n\n";
      $content.= "// Registra o namepspace da classe\n";
      $content.= "Ext.reg('".strtolower($class)."-module', {$class}Module );";
      
      return $content;
    }

    /**
     * Merges any number of arrays / parameters recursively, replacing
     * entries with string keys with values from latter arrays.
     * If the entry or the next value to be assigned is an array, then it
     * automagically treats both arguments as an array.
     * Numeric entries are appended, not replaced, but only if they are
     * unique
     *
     * calling: result = array_merge_recursive_distinct(a1, a2, ... aN)
     */
    private static function array_merge_recursive_distinct() {
      $arrays = func_get_args();
      $base = array_shift($arrays);
      if(!is_array($base)) $base = empty($base) ? array() : array($base);
      foreach($arrays as $append) {
        if(!is_array($append)) $append = array($append);
        foreach($append as $key => $value) {
          if(!array_key_exists($key, $base) and !is_numeric($key)) {
            $base[$key] = $append[$key];
            continue;
          }
          if(is_array($value) or is_array($base[$key])) {
            $base[$key] = self::array_merge_recursive_distinct($base[$key], $append[$key]);
          } else if(is_numeric($key)) {
            if(!in_array($value, $base)) $base[] = $value;
          } else {
            $base[$key] = $value;
          }
        }
      }
      return $base;
    }
}
