<?php
/**
 * ExtList
 *
 * Implementa funções de filtros para as tabelas
 *
 * @package     Doctrine
 * @subpackage  Template
 * @version     $Revision$
 * @author      Otávio Augusto <otavio@neton.com.br>
 */
class ExtList extends Doctrine_Template
{
    /**
     * Filtra um registro de acordo com o id da tabela principal de relacionamento
     *
     * @param Doctrine_Collection $collection
     * @param integer             $count
     * 
     * @return array Registros encontrados
     */
    public function extListTableProxy($collection, $count = -1)
    {
      // define o número total de registros, se ele não foi definido
      if ($count == -1)
      {
        $count = $collection->count();
      }
        
      //define o resultado
      return array(
        'total' => $count,
        'records' => $collection->toArray()
      );

    }

}
