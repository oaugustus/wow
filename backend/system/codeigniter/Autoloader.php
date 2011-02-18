<?php
/*
 * This file is part of Wow
 *
 * (c) 2010 Otávio Fernandes
 */
/**
 * Implements the CI class autoload schema.
 *
 * @package    CI
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class CI_Autoloader
{
    static $paths;
    static $fileList;
    
    /**
     * Define os paths onde serão buscados os arquivos das classes a serem
     * auto carregadas.
     * 
     * @param array $paths Paths onde tentarão ser localizados os arquivos das classes
     */
    static public function definePaths($paths)
    {
         foreach ($paths as $path)
         {
             $dir = new DirectoryIterator($path);

             self::$paths[] = $path;
             $h = array();

             foreach ($dir as $fileInfo)
             {
                  if($fileInfo->isDir() & !$fileInfo->isDot() & $fileInfo->getFilename() != '.svn')
                  {
                      $h[] = $path.$fileInfo->getFilename()."/";
                  }
             }

             if (count($h) > 0)
             {
                 self::definePaths($h);
             }
         }

    }

    static public function setPaths($p)
    {
      self::$paths = $p;
    }
    
    /**
     * Registra a classe CI_Autoloader como um autoloader SPL.
     */
    static public function register()
    {
        if (!file_exists(APPCACHE."require.php"))
        {
          $content = "<?php\n    \$fileList = array();";
          file_put_contents(APPCACHE."require.php", $content);          
        }
        
        // includes require file list
        require APPCACHE."require.php";
        
        self::$fileList = $fileList;

        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Manipula o autocarregamento de classes
     *
     * @param  string  $class  Um nome de classe.
     *
     * @return boolean Retorna true se a classe foi carregada
     */
    static public function autoload($class)
    {
        // define o nome completo do arquivo, com o seu path
        $file = self::getFile($class);

        // se o arquivo existir
        if ($file)
        {
          // inclui o arquivo
          require $file;
        }
        else
        {
          // arquivo não incluído na lista de includes
          return false;
        }
        
        // arquivo foi incluído na lista de includes
        return true;        
    }

    /**
     *  Retorna o arquivo da classe a ser instanciada procurando-o na lista
     *  de paths disponíveis. Retorna false, caso não encontre o arquivo.
     *
     *  @param string $class Nome da classe
     *
     *  @return Mixed Nome do arquivo, caso ele exista, false caso contrário
     */
    static private function getFile($class)
    {
        if (array_key_exists($class, self::$fileList))
        {
          return self::$fileList[$class];
        }
        /**
         * @todo check this code and autoload proccess
         */
        else if($class == 'CI_DB' || $class == 'auto')
        {
          return false;
        }
        else
        {
          for ($i = 0; $i < count(self::$paths); $i++)
          {
              //echo "$i<br>";

              // seta o path atual da lista de paths
              $path = self::$paths[$i];

              // verifica se o arquivo existe
              if (file_exists($path.str_replace('_', '/', $class).'.php'))
              {
                self::$fileList[$class] = $path.str_replace('_', '/', $class).'.php';

                self::rewriteRequireList(self::$fileList);
                
                return $path.str_replace('_', '/', $class).'.php';
              }
          }
        }
        // o arquivo não foi localizado
        return false;
        
    }

    /**
     * Rewrite the require.php fileList.
     * 
     * @param array $list
     */
    static private function rewriteRequireList($list)
    {
        $content = "<?php\n";
        $content.= '$fileList'." = array(\n";

        ksort($list);
        
        foreach ($list as $class => $path)
        {
          $content.= "    '".$class."' => '".$path."',\n";
        }

        $content.= ");";

        file_put_contents(APPCACHE."require.php", $content);
      
    }

}