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
class Doctrine_Template_Foto extends Doctrine_Template
{
    /**
     * Options
     *
     * @var array
     */
    protected $_options = array(
      'fotoField' => 'fotopath',
      //'nameField' => 'id',
      'path' => ''
    );

    /**
     * __construct
     *
     * @param array $options
     * @return void
     */
    public function __construct(array $options = array())
    {
	    parent::__construct($options);
    }

    public function setTableDefinition()
    {
        $this->addListener(new FotoListener($this->_options));
    }
}
