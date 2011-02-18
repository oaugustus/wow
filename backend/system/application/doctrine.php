<?php
/**
 * Configuração da ferramenta de linha de comando do doctrine.
 *
 * @package   <package>
 * @author    Otávio Fernandes <otavio@neton.com.br>
 * @copyright Copyright (c) 2010, Net On
 * @version   SVN: $Id$
 */
require_once('config/database.php');

// carrega a classe autoloader do CI
require_once '../codeigniter/Autoloader.php';

// define os paths onde deverão ser auto-carregadas as classes
CI_Autoloader::definePaths(
   array(
       'controllers/',
       'actions/',
       'libraries/',
       'models/'
   )
);

// registra o autoloader do CI
CI_Autoloader::register();

// Configure Doctrine Cli
// Normally these are arguments to the cli tasks but if they are set here the arguments will be auto-filled
$config = array('data_fixtures_path'  =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/fixtures',
                'models_path'         =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/models',
                'migrations_path'     =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/migrations',
                'sql_path'            =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/sql',
                'actions_path'        =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/actions',
                'action_package'      =>  '',
                'libraries_path'      =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/libraries',
                'config_path'         =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/config',
                'app_path'            =>  dirname(__FILE__) . DIRECTORY_SEPARATOR,
                'web_path'            =>  realpath(dirname(__FILE__) . '/../../..').'/web',
                'ui_path'             =>  realpath(dirname(__FILE__) . '/../../..').'/web/js',
                'cache_path'          =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/cache',
                'yaml_schema_path'    =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . '/schema');

$cli = new Doctrine_Cli($config);
$cli->run($_SERVER['argv']);