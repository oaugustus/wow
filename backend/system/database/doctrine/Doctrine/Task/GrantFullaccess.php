<?php
/**
 * ciGenerateUi
 *
 * @package     CodeIgniter
 * @subpackage  ciaction
 * @author      Otávio Fernandes <otavio@neton.com.br>
 */
class ciGrantFullaccess extends Doctrine_Task
{
    public $description          =   'Grant Full access to all modules for a group',
           $requiredArguments    =   array('group' => 'The group name or id',
                                           'config_path' => 'Specify the complete path to your config directory.',
                                           'app_path' => 'Specify the complete path to your application directory.'),
           $optionalArguments    =   array();
    
    public function execute()
    {
        // chama a tarefa para gerar a interface
        Doctrine_CI_SecurityTask::grantFullAccess($this->getArguments());

        // notifica o resultado
        $this->notify(sprintf('Full access granted to '.$this->getArgument('group').' group!'));
    }
}