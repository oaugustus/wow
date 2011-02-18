<?php
/**
 * ciGenerateUi
 *
 * @package     CodeIgniter
 * @subpackage  ciaction
 * @author      Otávio Fernandes <otavio@neton.com.br>
 */
class ciGenerateUi extends Doctrine_Task
{
    public $description          =   'Generate a ExtJS User Interface for models based on config/generator.yml',
           $requiredArguments    =   array('application' => 'Specify the application to generate de UI',
                                           'config_path' => 'Specify the complete path to your config directory.',
                                           'app_path' => 'Specify the complete path to your application directory.',
                                           'web_path' => 'Specify the complete path to your web path directory.',
                                           'ui_path' => 'Specify the complete path to UI files.'),
           $optionalArguments    =   array();
    
    public function execute()
    {
        // chama a tarefa para gerar a interface
        Doctrine_CI_UITask::generateUI($this->getArguments());

        // chama a tarefa para gerar a classe de autoinclusão dos scripts de interface
        Doctrine_CI_BuildAutoloadTask::build($this->getArguments());

        // notifica o resultado
        $this->notify(sprintf('Generated ExtJS UI classes and Autoload script files class successfully!'));
    }
}