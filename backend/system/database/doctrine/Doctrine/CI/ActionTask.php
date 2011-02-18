<?php
/**
 * Description of ciTask.
 *
 * @package    CodeIgniter
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class Doctrine_CI_ActionTask
{
    public static function generateAction($param)
    {
      // se a classe for uma classe válida
      if (Doctrine_Core::isValidModelClass($param['class_name']))
      {
        self::buildAutoAction($param);
        self::buildModelAction($param);
      }
      else
      {
        die("Invalid model class!");
      }        
    }

    private static function buildAutoAction($param)
    {
        $templateAction = $param['libraries_path']."/action/autoAction.php";
        $applicationPath = $param['actions_path']."/".$param['application'];
        $autoFile = file_get_contents($templateAction);
        $autoFile = str_replace('autoAction', 'auto'.$param['class_name'].'Action', $autoFile);
        $autoFile = str_replace('class_name', $param['class_name'], $autoFile);

        // criação do diretório do pacote
        if (!file_exists($applicationPath))
        {
          mkdir($applicationPath);
        }

        // criação do diretório auto dentro do pacote
        if (!file_exists(($applicationPath."/auto")))
        {
          mkdir($applicationPath."/auto");
        }

        // criação do arquivo auto
        file_put_contents($applicationPath."/auto/auto".$param['class_name']."Action.php", $autoFile);
    }

    private static function buildModelAction($param)
    {
        $templateAction = $param['libraries_path']."/action/action.php";
        $applicationPath = $param['actions_path']."/".$param['application'];

        if (!file_exists($applicationPath."/".$param['class_name']."Action.php"))
        {
          $file = file_get_contents($templateAction);
          $file = str_replace('autoAction', 'auto'.$param['class_name'].'Action', $file);
          $file = str_replace(' Action', " ".$param['class_name'].'Action', $file);
          $file = str_replace('class_name', strtolower($param['class_name']), $file);

          // criação do arquivo da ação
          file_put_contents($applicationPath."/".$param['class_name']."Action.php", $file);

        }
      
    }
}
