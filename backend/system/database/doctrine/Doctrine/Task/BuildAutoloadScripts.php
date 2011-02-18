<?php
/**
 * ciGenerateUi
 *
 * @package     CodeIgniter
 * @subpackage  ciaction
 * @author      Otávio Fernandes <otavio@neton.com.br>
 */
class ciBuildAutoloadScripts extends Doctrine_Task
{
    public $description          =   'Generate the autoload scripts class',
           $requiredArguments    =   array('web_path' => 'Specify the complete path to your web path directory.',
                                           'ui_path' => 'Specify the complete path to UI files.'),
           $optionalArguments    =   array();
    
    public function execute()
    {
        Doctrine_CI_BuildAutoloadTask::build($this->getArguments());
        
        $this->notify(sprintf('Generated Script Autoload classe successfully!'));
    }
}