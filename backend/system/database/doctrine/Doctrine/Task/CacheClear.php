<?php
/**
 * ciGenerateAction
 *
 * @package     CodeIgniter
 * @subpackage  ciaction
 * @author      Otávio Fernandes <otavio@neton.com.br>
 */
class ciCacheClear extends Doctrine_Task
{
    public $description          =   'Clear ExtDirect API cache',
           $requiredArguments    =   array('cache_path' => 'Path do diretório de cache'),
           $optionalArguments    =   array();
    
    public function execute()
    {
        Doctrine_CI_CacheClearTask::clear($this->getArguments());
        
        $this->notify(sprintf('Cache clear successfully'));
    }
}