<?php
/**
 * Classe que disponibiliza o método estático para a tarefa de criação
 * limpeza do diretório application/cache
 *
 * @package    CodeIgniter
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class Doctrine_CI_CacheClearTask
{
    /**
     * Limpa do diretório cache.
     * 
     * @param array $param configurações
     */
    public static function clear($param)
    {
      // recupera o path dos scripts e da pasta web
      $cachePath = $param['cache_path'];

      // variável de scripts
      $scripts = array();

      $ite=new RecursiveDirectoryIterator($cachePath);

      foreach (new RecursiveIteratorIterator($ite) as $filename=>$cur) {
          @unlink($filename);
      }

    }

}
