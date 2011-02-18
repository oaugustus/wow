<?php
/**
 * Fornece funções úteis para manipular o JSON.
 *
 * @package    
 * @author     Otávio Fernandes <otavio@neton.com.br>
 * @version    SVN: $Id$
 */
class JsonUtil
{
  /**
   * Formata uma string json com tabulações e espaçamentos.
   * 
   * @param string $json JSON não formatado
   * 
   * @return string JSON formatado com tabulações 
   */
  public static function format_($json)
  {
    $tab = "  ";
    $new_json = "";
    $indent_level = 0;
    $in_string = false;

    $len = strlen($json);

    for($c = 0; $c < $len; $c++)
    {
      $char = $json[$c];
      switch($char)
      {
        case '{':
        case '[':
          if(!$in_string)
          {
            $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
            $indent_level++;
          }
          else
          {
            $new_json .= $char;
          }
        break;
        case '}':
        case ']':
          if(!$in_string)
          {
            $indent_level--;
            $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
          }
          else
          {
            $new_json .= $char;
          }
        break;
        case ',':
          if(!$in_string)
          {
            $new_json .= ",\n" . str_repeat($tab, $indent_level);
          }
          else
          {
            $new_json .= $char;
          }
        break;
        case ':':
          if(!$in_string)
          {
            $new_json .= ": ";
          }
          else
          {
            $new_json .= $char;
          }
        break;
        case "(":
        case ")":
          $c++;
        break;
        case '"':
          if ($c > 0 && $json[$c-1] != '\\')
          {
            $in_string = !$in_string;
          }
        default:
          $new_json .= $char;
        break;
      }
    }

    return $new_json;
  }

  /**
   * Indents a flat JSON string to make it more human-readable
   *
   * @param string $json The original JSON string to process
   * @return string Indented version of the original JSON string
   */
  public static function format($json) {

      $result    = '';
      $pos       = 0;
      $strLen    = strlen($json);
      $indentStr = '  ';
      $newLine   = "\n";

      for($i = 0; $i <= $strLen; $i++) {

          // Grab the next character in the string
          $char = substr($json, $i, 1);

          // If this character is the end of an element,
          // output a new line and indent the next line
          if($char == '}' || $char == ']') {
              $result .= $newLine;
              $pos --;
              for ($j=0; $j<$pos; $j++) {
                  $result .= $indentStr;
              }
          }

          // elimina os valores literais
          if ($char == '"')
          {
            $next = substr($json, $i+1, 1);
            $prior = substr($json, $i-1, 1);
            
            if ($next == '`' | $prior == '`')
            {
              $char = '';
            }
          }

          if ($char == '`' || $char == '`')
          {
            $char = '';
          }
          // Add the character to the result string
          $result .= $char;

          // If the last character was the beginning of an element,
          // output a new line and indent the next line
          if ($char == ',' || $char == '{' || $char == '[') {
              $result .= $newLine;
              if ($char == '{' || $char == '[') {
                  $pos ++;
              }
              for ($j = 0; $j < $pos; $j++) {
                  $result .= $indentStr;
              }
          }
      }

      return $result;
  }
}
