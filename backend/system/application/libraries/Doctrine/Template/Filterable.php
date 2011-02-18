<?php
/**
 * Filterable
 *
 * Implementa funções de filtros para as tabelas
 *
 * @package     Doctrine
 * @subpackage  Template
 * @version     $Revision$
 * @author      Otávio Augusto <otavio@neton.com.br>
 */
class Filterable extends Doctrine_Template
{
    /**
     * Encontra registros aplicando filtro
     *
     * @param array $selections Campos a serem selecionados
     * @param array $params     Parâmetros com os filtros e paginações
     * @param Doctrine_Hydrator $hydration
     * @return array Registros encontrados
     */
    public function applyFilterTableProxy($selections, $params, $customFilters = array(), $hydration = Doctrine::HYDRATE_RECORD)
    {
        //LogManager::info('Start');
        $result = array();

        $customFilters = array_merge_recursive($customFilters,array('OR'=>array(),'AND' => array()));

        //Armazena o nome da classe modelo
        $model = $this->getInvoker()->getTable()->getComponentName();

        if (empty($params['basic']) && isset($params['fields']))
        {
          $params['basic'] = array(
              'fields' => $params['fields'],
              'query' => isset($params['query']) ? $params['query'] : ''
          );
        }
        
        //Se não existir o parâmetro limit
        if (empty ($params['limit']))
        {
            $result = $this->filterNoPager($selections, $params, $model, $customFilters, $hydration);
        }
        //Se existir parâmetro limit (paginação)
        else
        {
            $result = $this->filterWithPager($selections, $params, $model, $customFilters, $hydration);
        }
        //LogManager::info('End');
        return $result;
    }

    /**
     * Aplica os filtros à consulta sem criar paginação
     *
     * @param array  $selections
     * @param array  $params
     * @param string $component 
     * @param Doctrine_Hydrator $hydration
     * 
     * @return Mixed Registros encontrados
     */
    protected function filterNoPager(array $selections, array $params, $component, $customFilters, $hydration)
    {
        $selections = $this->getSelections($selections, $component);
        
        //cria a query
        $q = Doctrine_Query::create()
             ->select($selections['select'])
             ->from($selections['from']);
        
        //aplica os filtros à consulta
        $q = $this->setFilters($q, $params, $customFilters, explode(',',$selections['from']));

        // order by
        if (isset($params['sort']))
        {
          $q->orderBy($params['sort'].' '.$params['dir']);
        }

        //define o resultado
        $result = array(
            'total' => $q->count(),
            'records' => $this->fixScalar($q->execute(array(),Doctrine::HYDRATE_SCALAR))
        );

        //retorna o resultado
        return $result;
    }
    
    /**
     * Aplica os filtros à consulta criando uma paginação
     *
     * @param array $selections
     * @param array $params
     * @param string $component
     * @param Doctrine_Hydrator $hydration
     * @return Mixed Registros encontrados
     */
    protected function filterWithPager(array $selections, array $params, $component, $customFilters, $hydration)
    {
        $selections = $this->getSelections($selections, $component);

        //cria a query
        $q = Doctrine_Query::create()
             ->select($selections['select'])
             ->from($selections['from'])
             ->limit($params['limit'])
             ->offset($params['start']);
        
        //aplica os filtros à consulta
        $q = $this->setFilters($q, $params, $customFilters, explode(',',$selections['from']));

        // order by
        if (isset($params['sort']))
        {
          $q->orderBy($params['sort'].' '.$params['dir']);
        }

     //   echo $q->
       // echo $q->getSqlQuery();
        //echo $q->getDql();

        //cria a paginação
        if ($params['start'] == 0)
        {
          $params['start'] = $params['limit'];
        }
        
        $pager = new Doctrine_Pager($q, $params['start']/$params['limit'], $params['limit']);


        //define o resultado
        $result = array(
            'total' => $q->count(),
            //'records' => $this->fixScalar($pager->execute(array(),Doctrine::HYDRATE_SCALAR))
          'records' => $this->fixScalar($q->execute(array(),Doctrine::HYDRATE_SCALAR))
        );

        //retorna o resultado
        return $result;
    }

    /**
     * Seta os filtros a serem aplicados à consulta
     * 
     * @param Doctrine_Query $q
     * @param array $params
     * @params array $relations
     * @return Doctrine_Query Query com os devidos filtros aplicados
     */
    protected function setFilters(Doctrine_Query $q, array $params, $customFilters, array $relations)
    {
        if (!isset($params['query']))
        {
          $params['query'] = '';
        }
        
        //Armazena a tabela
        $table = $this->getInvoker()->getTable();

        // se nos filtros existir um campo id,
        if (!empty($params['fields']) && in_array('id', $params['fields']))
        {
          // adiciona pesquisa pelo id
          $q->addWhere('c.id =?', $params['query']);

          // retorna a consulta criada somente com a pesquisa pelo id
          return $q;
        }

        // apply the custom filters on query
        $this->setCustomOrFilters($q, $customFilters);

        if (isset($params['basic']))
        {
          if (!isset($params['advanced']) || count($params['advanced']) <= 0)
          {
            // apply basic filters
            $this->setBasicFilters($q, $table, $params, $relations);            
          }
          
        }


        
        if (isset($params['advanced']))
        {
          // apply advanced filters
          $this->setAdvancedFilters($q, $params);          
        }

        // apply custom and filters
        $this->setCustomAndFilters($q, $customFilters);

        //echo $q->getDql();
        
        return $q;
    }

    /**
     * Apply the advanced filters.
     *
     * @param Doctrine_Query $q
     * @param array $params
     */
    private function setAdvancedFilters($q, $params)
    {
      // reseta os valores
      $condition = array();
      $values = array();

      $filters = $params['advanced'];
      
      if (is_array($filters))
      {
          $encoded = false;
      }
      else
      {
          $encoded = true;
          $filters = json_decode($filters);
      }

      if (is_array($filters))
      {
          for ($i=0;$i<count($filters);$i++)
          {
              $filter = $filters[$i];

              // assign filter data (location depends if encoded or not)
              if ($encoded)
              {
                  $field = $filter->field;
                  $value = $filter->value;
                  $compare = isset($filter->comparison) ? $filter->comparison : null;
                  $filterType = $filter->type;
              } else
              {
                  $field = $filter['field'];
                  $value = $filter['value'];
                  $compare = isset($filter['comparison']) ? $filter['comparison'] : null;
                  $filterType = $filter['type'];
              }

              if (!preg_match('/\./', $field))
              {
                $field = 'c.'.$field;
              }
              
              switch($filterType)
              {
                  case 'string' : 
                    $condition[] = $field." stringSearch";
                    $values[] = "%$value%";
                  break;
                  case 'list' :
                    $q->andWhereIn($field, $value);

                      /*if (strstr($value,','))
                      {
                          $fi = explode(',',$value);
                          for ($k=0;$k<count($fi);$k++)
                          {
                              $fi[$k] = "'".$fi[$k]."'";
                          }
                          $condition[] = $field." listSearch";
                          $value = implode(',',$fi);
                          $values[] = $value;
                      }
                      else
                      {
                          $condition[] = $field;
                          $values[] = $value;
                      }*/
                  break;
                  case 'boolean' :
                    $condition[] = $field;
                    $values[] = $value;
                  break;
                  case 'numeric' :
                  case 'date':
                      switch ($compare)
                      {
                          case 'eq' : 
                            $condition[] = $field." gt";
                            $values[] = $value." 00:00:00";
                            $condition[] = $field." lt";
                            $values[] = $value." 23:59:59";
                          break;
                          case 'lt' : 
                            $condition[] = $field." lt";
                            $values[] = $value;
                          break;
                          case 'gt' : 
                            $condition[] = $field." gt";
                            $values[] = $value;
                          break;
                      }
                  break;
              }
          }
      }

      if (!empty ($condition))
      {
        $str = $this->setSearchTypes(implode(' AND ',$condition));
        //echo $str;
        //print_r($values);
        
        $q->andWhere($str,$values);

      }
      
    }

    /**
     * Sets the search types of advanced filters
     *
     * @param string $str
     * 
     * @return string
     */
    private function setSearchTypes($str)
    {
      $str = str_replace('lt',' <? ',$str);
      $str = str_replace('gt',' >? ',$str);
      $str = str_replace('eq',' =? ',$str);
      $str = str_replace('listSearch',' in(?)',$str);
      $str = str_replace('stringSearch',' LIKE ?',$str);

      //echo $str;
      
      return $str;
    }

    /**
     * Apply basic filters into query
     */
    private function setBasicFilters($q, $table, $params, $relations)
    {
        $params = $params['basic'];
        
        // se a query não foi especificada, cria uma vazia
        if (empty($params['query']))
        {
          $params['query'] = '';
        }

        // reseta os valores
        $condition = array();
        $value = array();


        //Se existirem filtros a serem aplicados
        if (!empty ($params['fields']))
        {
            //Recupera somente os filtros
            $filters = $params['fields'];

            //percorre a lista de campos adicionando-os ao filtro da query
            foreach ($filters  as $filter)
            {
                //Se a coluna existir na tabela
                if ($table->hasColumn($filter) && $params['query'] != '')
                {
                  //$q->orWhere("c.".$filter." LIKE ?", "%".$params['query']."%");
                  $condition[] = 'c.'.$filter;
                  $value[] = "%".$params['query']."%";

                }
                else if (count(explode($filter,'.')) > 1)
                {
                  //$q->orWhere($filter." LIKE ?","%".$params['query']."%");
                  $condition[] = 'c.'.$filter;
                  $value[] = "%".$params['query']."%";
                }
                else
                {
                    unset($relations[0]);

                    foreach ($relations as $relation)
                    {

                        $rFilter = explode('.',$filter);

                        $alias = explode('.',$relation);

                        $a = explode(" ",$alias[1]);

                        // se a tabela possuir o relacionamento
                        if ($table->hasRelation($a[0]))
                        {
                          $instance = $table->getRelation(($a[0]))->getTable();
                          if ($instance->hasColumn(end($rFilter))  && $params['query'] != '')
                          {
                            //$q->orWhere($filter." LIKE ?", "%".$params['query']."%");
                            $condition[] = $filter;
                            $value[] = "%".$params['query']."%";
                          }

                        }
                    }
                }
            }

        }

        if (count($condition) > 0)
        {
          $str = implode(' LIKE ? OR ',$condition);
          $q->andWhere($str." LIKE ? ",$value);
        }
      
    }
    
    /**
     * Apply custom filters in query.
     * 
     * @param Doctrine_Query $q
     * @param array $customFilters
     */
    private function setCustomOrFilters($q, $customFilters)
    {
        // reseta os valores
        $condition = array();
        $value = array();

        // adiciona os filtros OR customizados
        foreach ($customFilters['OR'] as $c => $v)
        {
          $condition[] = $c;
          $value[] = $v;
        }

        if (count($condition) > 0)
        {
          $str = implode(' OR ',$condition);
          $q->orWhere($str,$value);
        }      
    }

    /**
     * Apply custom filters in query.
     *
     * @param Doctrine_Query $q
     * @param array $customFilters
     */
    private function setCustomAndFilters($q, $customFilters)
    {
        // reseta os valores
        $condition = array();
        $value = array();

        // adiciona os filtros OR customizados
        foreach ($customFilters['AND'] as $c => $v)
        {
          $condition[] = $c;
          $value[] = $v;
        }

        if (count($condition) > 0)
        {
          $str = implode(' AND ',$condition);
          $q->andWhere($str,$value);
        }
    }


    /**
     * Recupera a relação de campos da seleção definidos na
     * classe Record
     *
     * @param array $selections
     * @param string $component
     * @return String campos de seleção
     */
    private function getSelections($selections, $component)
    {
       $fields = $selections['fields'];
       $relations = $selections['relations'];

       foreach ($fields as $field)
       {
           $selectedFields[] = $field;
       }

       $selectedRelations[] = $component.' c';
       
       foreach ($relations as $relation => $alias)
       {
           $a = explode('.',$relation);

           if (count($a) == 2)
           {
             $selectedRelations[] = $relation." ".$alias;
           }
           else
           {
             $selectedRelations[] = 'c.'.$relation." ".$alias;
           }
       }
       
       return array(
           'select' => implode(',',$selectedFields),
           'from' => implode(',',$selectedRelations)
       );
    }

    private function fixScalar($data)
    {
      $array = array();

      for ($i = 0; $i < count($data); $i++)
      {
        foreach ($data[$i] as $key => $value)
        {
          $newKey = explode('_',$key);
          if (count($newKey) > 2)
          {
            unset($newKey[0]);
            $newKey = implode('_',$newKey);
          }
          else
          {
            $newKey = $newKey[1];
          }
          
          $array[$i][$newKey] = $value;
        }        
      }

      return $array;

    }
}
