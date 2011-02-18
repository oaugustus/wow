<?php
/**
 * ciGenerateUi
 *
 * @package     CodeIgniter
 * @subpackage  ciaction
 * @author      Otávio Fernandes <otavio@neton.com.br>
 */
class ciLoadModules extends Doctrine_Task
{
    public $description          =   'Load Modules into database',
           $requiredArguments    =   array('application' => 'Specify the application to generate de UI',
                                           'config_path' => 'Specify the complete path to your config directory.',
                                           'app_path' => 'Specify the complete path to your application directory.'),
           $optionalArguments    =   array();
    
    public function execute()
    {
        // chama a tarefa para gerar a interface
        Doctrine_CI_LoadModulesTask::load($this->getArguments());

        // chama a tarefa para gerar o carregamento dos módulos
        Doctrine_CI_SecurityTask::load($this->getArguments());

        // notifica o resultado
        $this->notify(sprintf('Modules for '.$this->getArgument('application').' application loaded successfuly!'));
    }
}