<?php
include "baseload.php";

/**
 * Autoloading js scripts class
 * 
 */
class JSLoad extends BaseLoad
{
  /**
   * Load JavaScript scripts
   */
  public static function load()
  {
    // reorder list
    //self::reorder();

    // autload the scripts
    foreach (self::$scripts as $script)
    {
      echo self::buildScript($script);
    }
  }

  /**
   * Build a script line to inclusion.
   * 
   * @param string $source Source file to include
   *
   * @return string Script include line
   */
  private function buildScript($source)
  {
    return "<script type='text/javascript' src='$source'></script>";
  }

  /**
   * Reorder the scripts list
   */
  private function reorder()
  {
    
  }
}