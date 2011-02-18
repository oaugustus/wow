<?php
/**
 * ciGenerateUi
 *
 * @package     CodeIgniter
 * @subpackage  ciaction
 * @author      Otávio Fernandes <otavio@neton.com.br>
 */
class ciLoadPermissions extends Doctrine_Task
{
    public $description          =   'Load Permissions into database',
           $requiredArguments    =   array('config_path' => 'Specify the complete path to your config directory.',
                                           'app_path' => 'Specify the complete path to your application directory.'),
           $optionalArguments    =   array();
    
    public function execute()
    {
        // chama a tarefa para gerar a interface
        Doctrine_CI_SecurityTask::load($this->getArguments());

        // notifica o resultado
        $this->notify(sprintf('Permissions loaded successfuly!'));
    }
}