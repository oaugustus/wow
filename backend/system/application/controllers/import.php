<?php
/**
 * Description of import
 *
 * @author Otávio
 */
class Import extends Controller
{
    private $pessoa_id = 1;
    private $path = UPLOADPATH;

    public function formatImages()
    {
       $dir = new DirectoryIterator($this->path."modelo/");

       foreach ($dir as $fileInfo)
       {
            if(!$fileInfo->isDir() & !$fileInfo->isDot())
            {
                echo $fileInfo->getFilename()."<br>";
                //rename($this->path."modelo/".$fileInfo->getFilename(), $this->path."modelo/".strtoupper(StringUtil::removeAccents(strtolower($fileInfo->getFileName()))));
            }
       }
      
    }

    public function setModeloImage()
    {
        $modelo = Doctrine_Core::getTable('Modelo')->findAll();

        foreach ($modelo as $m)
        {
            if (file_exists($this->path."modelo/".str_replace('Á', 'A',strtoupper(StringUtil::removeAccents($m->nome)).".JPG")))
            {
                $m->fotopath = "modelo/".strtoupper(strtoupper(StringUtil::removeAccents($m->nome).".JPG"));
                $m->fotopath = str_replace('Á', 'A', $m->fotopath);
            }
            else
            {
              $a = $m->nome;
              echo str_ireplace('á', 'A', $a)."<br>";
              echo str_ireplace('Á', 'A', $a)."<br>";
              $m->fotopath = 'no-photo.png';
            }

            $m->save();

        }

    }

    
    public function equipamento()
    {
        $q = Doctrine_Query::create()
             ->select('*')
             ->from('Imp')
             ->orderBy('id');
        
        $rs = $q->execute();        
        $list = $rs->toArray();
        $rs->free();
        
        
        for ($i = 1001; $i < count($list); $i++)
        {
          $item = $list[$i];
          
          $categoria = $this->getCategoria($item['categoria']);
          $fabricante = $this->getFabricante($item['fabricante']);
          
          if ($categoria && $fabricante)
          {
            $modelo = $this->getModelo($item['modelo'], $categoria->id, $fabricante->id);
            $setor = $this->getSetor($item['setor'], $this->pessoa_id);

            if ($modelo && $setor)
            {
              $equipamento = $this->getEquipamento($item);

              $equipamento->categoria_id = $categoria->id;
              $equipamento->proprietario_id = $this->pessoa_id;
              $equipamento->modelo_id = $modelo->id;
              $equipamento->setor_id = $setor->id;
              $equipamento->save();

              echo "<pre>";
              print_r($equipamento->toArray());
              echo "</pre>";
              
              $equipamento->free();
              $modelo->free();
              $setor->free();

            }

            $categoria->free();
            $fabricante->free();

          }

        }
    }

    /**
     * Recupera a categoria pelo seu nome.
     * 
     * @param String $name
     * @return Categoria
     */
    private function getCategoria($name)
    {
        if ($name)
        {
          $cat = Doctrine_Core::getTable('Categoria')->findOneBynome($name);

          if (!$cat)
          {
              $cat = new Categoria();
              $cat->nome = $name;
              $cat->save();
          }

          return $cat;
          
        }
        else
        {
          return false;
        }
    }

    /**
     * Recupera o fabricante pelo seu nome.
     * 
     * @param string $name
     */
    private function getFabricante($name)
    {
        if ($name != '')
        {
          $fab = Doctrine_Core::getTable('Pessoa')->findOneBynome($name);

          if (!$fab)
          {
              $fab = new Pessoa();
              $fab->tipo = 'J';
              $fab->fabricante = '1';
              $fab->nome = $name;
              $fab->save();
          }

          return $fab;
          
        }
        else
          return false;
    }

    /**
     * Recupera o modelo pelo seu nome.
     *
     * @param string $name
     */
    private function getModelo($name, $categoria, $fabricante_id)
    {
        if ($name)
        {
          $q = Doctrine_Query::create()
               ->select('*')
               ->from('Modelo')
               ->where('fabricante_id =?',$fabricante_id)
               ->andWhere('nome =?', $name);

          $mod = $q->fetchOne();

          $q->free();

          if (!$mod)
          {
              $mod = new Modelo();
              $mod->fabricante_id = $fabricante_id;
              $mod->nome = $name;
              $mod->categoria_id = $categoria;
              $mod->save();
          }

          return $mod;

        }
        else
        {
          return false;
        }
    }

    /**
     * Recupera o i pedlo seu nome.
     *
     * @param string $name
     */
    private function getSetor($name, $pessoa_id)
    {
        if ($name)
        {
          $q = Doctrine_Query::create()
               ->select('*')
               ->from('Setor')
               ->where('pessoa_id =?',$pessoa_id)
               ->andWhere('nome =?',$name);

          $set = $q->fetchOne();

          $q->free();

          if (!$set)
          {
              $set = new Setor();
              $set->pessoa_id = $pessoa_id;
              $set->nome = $name;
              $set->save();
          }

          return $set;
          
        }
        else
        {
          return false;
        }
    }

    private function getEquipamento($item)
    {
        $numeroserie = ($item['numeroserie'] != '' ) ? $item['numeroserie'] : 0;
        
        $eqp = Doctrine_Core::getTable('Equipamento')->findOneBynumeroserie($numeroserie);

        if (!$eqp)
        {
            $numeropatrimonio = ($item['numeropatrimonio'] != '' ) ? $item['numeropatrimonio'] : 0;
            $eqp = Doctrine_Core::getTable('Equipamento')->findOneBynumeropatrimonio($item['numeropatrimonio']);
        }

        if (!$eqp)
        {
            $eqp = new Equipamento();
            $eqp->numeroserie = $item['numeroserie'];
            $eqp->numeropatrimonio = $item['numeropatrimonio'];
            $eqp->informacoestecnicas = $item['informacoestecnicas'];
        }

        return $eqp;
    }

}

