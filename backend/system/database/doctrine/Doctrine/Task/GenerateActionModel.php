<?php
/**
 * ciGenerateAction
 *
 * @package     CodeIgniter
 * @subpackage  ciaction
 * @author      Otávio Fernandes <otavio@neton.com.br>
 */
class ciGenerateActionModel extends Doctrine_Task
{
    public $description          =   'Generate a Action class for a Model',
           $requiredArguments    =   array('class_name' => 'Name of the action class to generate',
                                           'application' => 'Name of application to generate action class into',
                                           'libraries_path' => 'Name of package to generate action class into',
                                           'actions_path' => 'Specify the complete path to your migration classes folder.'),
           $optionalArguments    =   array();
    
    public function execute()
    {
        Doctrine_CI_ActionTask::generateAction($this->getArguments());
        
        $this->notify(sprintf('Generated action class: successfully to %s', $this->getArgument('class_name')));
    }
}