<?php
/**
 * Funções úteis para a manipulação de números
 *
 * @author Otávio
 */
class NumberUtil
{
    /**
     * Força a conversão de um número em qualquer formato "texto" para um número
     *
     * @param string $str_number
     * 
     * @return float 
     */
    public static function parseNumber($str_number)
    {
      if (!is_numeric($str_number))
      {
        return str_replace(',','.',str_replace('.', '', $str_number));
      }
      else
        return $str_number;
       
    }
}
?>
