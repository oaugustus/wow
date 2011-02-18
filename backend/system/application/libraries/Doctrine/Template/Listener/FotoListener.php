<?php
/**
 * Foto
 *
 * Implementa template de fotos
 *
 * @package     Doctrine
 * @subpackage  Template
 * @version     $Revision$
 * @author      Otávio Augusto <otavio@neton.com.br>
 */
class FotoListener extends Doctrine_Record_Listener
{
    protected $field;
    
    public function __construct($options)
    {
       $this->field = $options['fotoField'];
       //$this->name = $options['nameField'];
       $this->path = $options['path'];
    }
    
    public function preInsert(Doctrine_Event $event)
    {
       //LogManager::info(json_encode(SessionManager::getRequestData()));
       
    }

    public function preUpdate(Doctrine_Event $event)
    {
        //print_r(SessionManager::getSession()->files);
        
        $file = SessionManager::getSession()->files[$this->field];
        
        if (isset($file))
        {
            if ($file['name'] != '')
            {
                //LogManager::info($file['name']." => ".$this->path."/".$event->getInvoker()->getFotoName());
                $field = $this->field;
                $fotoFile = $this->path.$event->getInvoker()->getFotoName().$this->getExtension($file['name']);
                $event->getInvoker()->$field = $fotoFile;
                move_uploaded_file($file['tmp_name'], UPLOADPATH.$fotoFile);
            }
        }
    }

    /**
     * Retorna a extensão do arquivo.
     * 
     * @param string $file
     * @return string
     */
    private function getExtension($file)
    {
        $parts = explode('.', $file);
        
        return ".".end($parts);
    }
}
