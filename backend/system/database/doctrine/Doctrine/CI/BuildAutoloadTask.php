<?php
/**
 * Classe que disponibiliza o método estático para a tarefa de criação da classe de autocarregamento de scripts.
 *
 * @package    CodeIgniter
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class Doctrine_CI_BuildAutoloadTask
{
    public static function build($param)
    {
      // recupera o path dos scripts e da pasta web
      $autoloadPath = $param['web_path']."/autoload/";
      $uiPath = $param['ui_path'];

      // variável de scripts
      $scripts = array();

      $ite=new RecursiveDirectoryIterator($uiPath);

      foreach (new RecursiveIteratorIterator($ite) as $filename=>$cur) {
         if ($cur->getFilename() != 'xds_index.js')
         {
           if (substr($cur->getFilename(),-3) == '.js')
           {
             $scripts[] = str_replace($uiPath, 'js', $filename);
           }           
         }         
      }

      self::writeAutoloadClass($scripts, $autoloadPath);
    }

    /**
     * Escreve a classe base de autocarregamento de scripts
     */
    private static function writeAutoloadClass($scripts, $path)
    {
      $file  = "<?php\n";
      $file .= "  class BaseLoad\n";
      $file .= "  {\n";
      $file .= '    protected static $scripts = array('."\n";

      $put = array();

      arsort($scripts);
      
      foreach ($scripts as $script)
      {
        $script = str_replace('\\','/' ,$script);
        $put[] =  "      '$script'";
      }

      $file .= implode($put, ",\n");
      $file .= "\n    );\n";
      $file .= "  }";

      file_put_contents($path.'baseload.php', $file);
    }
}
