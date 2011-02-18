<?php

/*********************************************************************************
- Date operations by Andr� Cupini
- Sum or subtract day, months or years from any date
- Ex:

$date = $dt->operations("06/01/2003", "sum", "day", "4")   // Return 10/01/2003
$date = $dt->operations("06/01/2003", "sub", "day", "4")   // Return 02/01/2003
$date = $dt->operations("06/01/2003", "sum", "month", "4") // Return 10/05/2003
*********************************************************************************/
class DateUtil
{
	public static $days = array(1=>'seg',2=>'ter',3=>'qua',4=>'qui',5=>'sex',6=>'sab',0=>'dom');
	public static $longDays = array(1=>'SEGUNDA-FEIRA',2=>'TERÇA-FEIRA',3=>'QUARTA-FEIRA',4=>'QUINTA-FEIRA',5=>'SEXTA-FEIRA',6=>'S�BADO',0=>'DOMINGO');
	

    /**
     * Adiciona uma quantidade de dias à data.
     * 
     * @param pt_brDate $date
     * @param integer   $days
     * @param mixed     $return_format
     */
    public static function addDay($date, $days, $return_format = false)
    {
       return self::operations($date, 'sum', 'day',$days, $return_format);
    }

    /**
     * Adiciona uma quantidade de meses à data.
     *
     * @param pt_brDate $date
     * @param integer   $months
     * @param mixed     $return_format
     */
    public static function addMonth($date, $days, $return_format = false)
    {
       return self::operations($date, 'sum', 'month',$months, $return_format);
    }

    /**
     * Substitui o dia de uma determinada data por outro dia.
     *
     * @param Date    $date
     * $param integer $day
     */
    public static function replaceDay($date, $day)
    {
        $d = explode('/',$date);
        return str_pad($day, 2,'0',STR_PAD_LEFT)."/".$d[1]."/".$d[2];
    }
    
    /**
     * Retorna o número de meses entre duas datas.
     *
     * @param date     $start
     * @param date     $end
     * @return integer
     */
	public static function getMonthCount($start, $end)
	{
		$iniDate = explode('/',$start);
		$endDate = explode('/',$end);
		$mFim = $endDate[1];
		$mFim += ($endDate[2]-$iniDate[2])*12;
		$mFim -= $iniDate[1];

		return $mFim + 1;

	}

    /**
     * Retorna o intervalor de primeiros dias dos meses de uma data
     *
     * @param Date $start
     * @param Date $end
     *
     * @return array
     */
    public static function getFirstDayOfMonthInterval($start, $end)
    {
		$startTs = self::operations($start,'+','day',0,'ts');
		$endTs = self::operations($end,'+','day',0,'ts');
		$list = array();
		$refer = $startTs;

		$cont = 0;
		while($refer <= $endTs){
			$list[$cont] = date('Y-m-d',$refer);
			$refer = self::operations(date('d/m/Y',$refer),'+','month',1,'ts');
			$cont++;
		}

		return $list;
      
    }

	/**
	 * Converte uma data no formato 'd/m/Y' para o formato 'Y-m-d'
     *
     * @param Date $date
	 */
	public static function Str2Db($date)
    {
		$strRetorno = "";

		$data = split('/',$date);

		if (count($data) == 3){
			return $data[2].$data[1].$data[0];
		}else
			return '';

	}

	/**
	 *  Retorna uma data formatada no padrão 'd/m/Y'
	 */
	public static function Db2Str($date)
    {
		$strRetorno = "";
		$data = split('-',$date);

		return $data[2]."/".$data[1]."/".$data[0];

	}

    
	/**
	 * @param date $date data a ser somada
	 * @param string $operation opera��o a ser executada somar ou subtrair
	 * @param string $where	onde ser� somado o n�mero dia, m�s ou ano
	 * @param int $quant n�mero a ser somado na data
	 * @param string $return_format formato da data a ser retornada
	 * @description		Soma um n�mero inteiro a um dia, m�s ou ano de uma derterminada
	 * 					data 
	 */
	public static function operations($date, $operation, $where = FALSE, $quant, $return_format = FALSE)
	{
		// Verifica erros
		$warning = "<br>Warning! Date Operations Fail... ";
		if(!$date || !$operation) {
			return "$warning invalid or inexistent arguments<br>";
		}else{
			if(!($operation == "sub" || $operation == "-" || $operation == "sum" || $operation == "+")) return "<br>$warning Invalid Operation...<br>";
			else {
				// Separa dia, m�s e ano
				list($day, $month, $year) = split("/", $date);

				// Determina a opera��o (Soma ou Subtra��o)
				($operation == "sub" || $operation == "-") ? $op = "-" : $op = '';
				
				$sum_day = 0;
				$sum_month = 0;
				$sum_year = 0;
				// Determina aonde ser� efetuada a opera��o (dia, m�s, ano)
				if($where == "day")   $sum_day	 = $op."$quant";
				if($where == "month") $sum_month = $op."$quant";
				if($where == "year")  $sum_year	 = $op."$quant";
				
				// Gera o timestamp
				$date = mktime(0, 0, 0, $month + $sum_month, $day + $sum_day, $year + $sum_year);
				
				// Retorna o timestamp ou extended
				($return_format == "timestamp" || $return_format == "ts") ? $date = $date : $date = date("d/m/Y", "$date");

				// Retorna a data
				return $date;
			}
		}
	}
	
	/**
	 * @param int $month mes a ser utilizado como refer�ncia
	 * @param int $year ano a ser utilizado como refer�ncia
	 * @return array array com as semenas do m�s e os dias de cada semana
	 * @description		calcula o intervalo de dias das semanas de um m�s e retorna
	 * 					essas semanas em um array
	 */
	public static function getWeeks($month, $year, $dateFormat = 'd/m/Y')
    {
		$monthSize = date('t',mktime(0,0,0,$month,1,$year));
		$weeks = array();
		$contWeeks = 1;
		
		for ($i = 1; $i <= $monthSize; $i++){
			$dayofWeek = date('w', mktime(0,0,0,$month,$i,$year));
						
			$weeks[$contWeeks][$dayofWeek] = date($dateFormat,mktime(0,0,0,$month,$i,$year)); 

			if ($dayofWeek == 0){
				$contWeeks++;
			}				
			
		}
		
		return $weeks;
	}
		
	/**
	 * @param	array	$week	semana da qual ser� retirado o intervalo
	 */
	public static function getWeekInterval($week)
    {
		
		$contDays = count($week);
		$start = "";
		$end = "";
		$cont = 0;
		foreach ($week as $key => $value){
			if ($cont == 0)
				$start = $value;
			if ($cont = $contDays)
				$end = $value;
		} 
		
		return array($start,$end);
	}
	
	/**
	 * @param date $start data de in�cio
	 * @param date $end data de t�rmino
	 * @description	retorna um array com um intervalo de datas de $start a $end
	 */
	public static function getInterval($start, $end)
    {
		$startTs = $this->operations($start,'+','day',0,'ts');
		$endTs = $this->operations($end,'+','day',0,'ts');
		$list = array();
		$refer = $startTs;
		
		$cont = 0;
		while($refer <= $endTs){
			$list[$cont]['data'] = date('Ymd',$refer);
			$list[$cont]['day'] = date('w',$refer);
			$refer = $this->operations(date('d/m/Y',$refer),'+','day',1,'ts');
			$cont++;
		}
		
		return $list;
	}

	
	/**
	 * @param date	$date	data a ser utilizada na fun��o
	 */
	 public static function getDayOfWeek($date)
     {
		list($dia, $mes, $ano) = split('/',$date);	
		$nDia = date('w',mktime(0,0,0,$mes,$dia,$ano)); 	
		return $this->longDays[$nDia];
	 }

	/**
	 * @param date	$date	data a ser utilizada na fun��o
	 */
	 public static function getNShortDay($date)
     {
		list($dia, $mes, $ano) = split('/',$date);	
		$nDia = date('w',mktime(0,0,0,$mes,$dia,$ano)); 	
		return $this->days[$nDia];
	 }

	 
	 /**
	  * @description	Retorna a abreviatura do dia da semana
	  */
	 public static function getShortDay($nday)
     {
	 	return $this->days[$nday];
	 }
	 
}
?>