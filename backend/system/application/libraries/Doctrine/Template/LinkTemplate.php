<?php
/**
 * Link
 *
 * Implements functions to List a module with link relations
 *
 * @package     Doctrine
 * @subpackage  Template
 * @version     $Revision$
 * @author      Otávio Augusto <otavio@neton.com.br>
 */
class LinkTemplate extends Doctrine_Template
{
    /**
     * List a collection with links to a specified relation
     *
     * @param string $link      Model of relation
     * @param array  $in        Field of relationship
     * @param string $key       Value of key relationship
     * @param Dotrine_Query $q  Query to execute, if seted
     * @param string $as        Field mapped as
     * 
     * @return Doctrine_Collection List of selected records
     */
    public function listWithLinkTableProxy($link, $in, $key, $q = null, $as = 'selected')
    {
      // get the name of component
      $model = $this->getInvoker()->getTable()->getComponentName();

      // if the query is not seted
      if (!$q)
      {
        // create the query
        $q = Doctrine_Query::create()
             ->select('*')
             ->from($model);
      }

      $rs = $q->execute();

      
      // for each record in list
      for ($i = 0; $i < count($rs); $i++)
      {
        $pm = $rs[$i]->$link->toKeyValueArray(current($in),key($in));

        if (count($pm) > 0)
        {
          if (isset($pm[$key]))
          {
            $rs[$i]->mapValue($as,1);
          }
          else
          {
            $rs[$i]->mapValue($as,0);
          }
        }        
      }
      
      return $rs;

    }

}
