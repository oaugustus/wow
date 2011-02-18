<?php
/**
 * Fornece funções úteis para manipular logs do framework.
 *
 * @package
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class LogManager
{
    /**
     * Writer do Objeto de Log.
     * 
     * @var Zend_Log_Writer
     */
    private static $writer = null;

    /**
     * @var Zend_Log
     */
    private static $logger = null;
    
    /**
     * Cria o objeto de Log
     */
    private static function createLog()
    {
      // cria o writer
      //self::$writer = new Zend_Log_Writer_Firebug();
      self::$writer = new Zend_Log_Writer_Stream('log.txt');
      // cria o logger
      self::$logger = new Zend_Log(self::$writer);
    }

    /**
     * Cria um log do tipo info.
     * 
     * @param string $msg
     */
    public static function info($msg)
    {
      if (!self::$logger)
      {
        self::createLog();
      }
      
      self::$logger->info($msg);
    }
}
