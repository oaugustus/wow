<?php

/**
 * ApplicationTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ApplicationTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object ApplicationTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Application');
    }

    /**
     * Retorna os módulos de uma aplicação para um deternimado usuário de
     * acordo com suas permissões.
     * 
     * @param integer $user_id Id do usuário
     *
     * @return array Relação de Aplicações/Pacotes/Módulos a serem carregados para o usuário
     */
    public function loadModules($user_id)
    {
      $q = Doctrine_Query::create()
           ->select('a.*, p.*, m.*')
           ->from('Application a, a.Package p, p.Module m, m.Permission pe, pe.Privilege pr, pr.Group g, g.User u')
           ->where('u.id=?',$user_id)
           ->addWhere('m.active = "1"')
           ->orderBy('a.view_order ASC, a.id ASC, p.view_order ASC, p.id ASC, m.view_order ASC, m.id ASC');

      /* Debug
      $q = Doctrine_Query::create()
           ->select('a.*, p.*, m.*')
           ->from('Application a, a.Package p, p.Module m')
           ->orderBy('a.view_order ASC, a.id ASC, p.view_order ASC, p.id ASC, m.view_order ASC, m.id ASC');
      */
      
      $modules = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

      return $this->parseToWow($modules);
    }

    /**
     * Parseia as aplicações/pacotes/módulos que o usuário tem acesso para
     * o gerenciador de módulos do Wow
     * 
     * @param array $modules Aplicações do sistema
     *
     * @return array Módulos a serem retornados ao gerenciador de módulos
     */
    private function parseToWow($modules)
    {
      $m = array();

      foreach ($modules as $appCfg)
      {
        $app['title'] = $appCfg['title'];
        $app['description'] = $appCfg['description'];
        $app['items'] = array();

        foreach ($appCfg['Package'] as $packCfg)
        {
          $pack['title'] = $packCfg['title'];
          $pack['xtype'] = 'buttongroup';
          $pack['items'] = array();

          foreach ($packCfg['Module'] as $moduleCfg)
          {
            $module['text'] = $moduleCfg['title'];
            $module['description'] = $moduleCfg['description'];
            $module['iconCls'] = $moduleCfg['icon_class'];
            $module['mId'] = strtolower($moduleCfg['name']);
            $module['scale'] = 'large';
            $module['mtype'] = strtolower($moduleCfg['name']).'-module';

            $pack['items'][] = $module;
            unset($module);
          }

          $app['items'][] = $pack;
          unset($pack);
        }

        $m[] = $app;
        unset($app);
      }

      return $m;
    }
}