<?php
/**
 * Classe que contem metodos estaticos comum ao projeto
 * @package classes
 * @version 1.2
 * @package classes
 * @name Common
 */
class Common {

	/**
	 * Faz uma operacao matematica entre dois horarios no formato hh:ii:ss
	 *
	 * @example Common::calcularTempo('+','00:00:10','01:00:03');
	 * @param string $operacao
	 * @param string $hora_inicial
	 * @param string $hora_final
	 * @return string
	 */
	static public function calcularTempo($opercao, $hora_inicial, $hora_final) {
		$i = 1;
		$tempo_total;

		$tempos = array($hora_final, $hora_inicial);

		foreach ($tempos as $tempo) {
			$segundos = 0;

			list($h, $m, $s) = explode(':', $tempo);

			$segundos += $h * 3600;
			$segundos += $m * 60;
			$segundos += $s;

			$tempo_total[$i] = $segundos;

			$i++;
		}
		if ($opercao == "+")
			$segundos = $tempo_total[1] + $tempo_total[2];
		else if ($opercao == "-")
			$segundos = $tempo_total[1] - $tempo_total[2];
		else if ($opercao == "*")
			$segundos = $tempo_total[1] * $tempo_total[2];
		else if ($opercao == "/")
			$segundos = $tempo_total[1] / $tempo_total[2];

		$horas = floor($segundos / 3600);
		$segundos -= $horas * 3600;
		$minutos = str_pad((floor($segundos / 60)), 2, '0', STR_PAD_LEFT);
		$segundos -= $minutos * 60;
		$segundos = str_pad($segundos, 2, '0', STR_PAD_LEFT);

		return "$horas:$minutos:$segundos";
	}

	/**
	 * Transforma uma data de um formato $currFormat para $newFormat
	 *
	 * @example Common::trasformarData($_REQUEST["dataInicio"],"d-m-Y","Y-m-d");
	 * @param string $d
	 * @param string $currFormat
	 * @param string $newFormat
	 * @return string
	 */
	static public function trasformarData($d = "", $currFormat = "Y-m-d", $newFormat = "d-m-Y") {
		if (empty($d))
			return $d;
		if ($d == "")
			return $d;
		if ($d == "0000-00-00 00:00:00" || $d == "0000-00-00" || $d == "00-00-0000 00:00:00" || $d == "00-00-0000 00:00:00")
			return $d;
		if (stripos($d, "0000-00-00") !== false)
			return $d;
		try {
			$data = DateTime::createFromFormat($currFormat, $d);
		} catch (Exception $e) {
			Erro::displayDie($e -> getMessage());
		}
		return $data -> format($newFormat);
	}

	/**
	 * Retorna o nome (completo) de um mes dado seu numero $mes de 1 ate 12 (Janeiro ate Dezembro)
	 * @param int $mes
	 * @param boolean $semacentuacao=true : retorna o mes de nenhum tipo de caracter especial
	 * @return string
	 */
	static public function getMesNome($mes, $semacentuacao = true) {
		$meses = array('Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
		if ($semacentuacao)
			$meses = array('Janeiro', 'Fevereiro', 'Marco', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
		if ($mes > 12 || $mes < 1)
			return '';
		$mesAtual = $meses[$mes - 1];
		return $mesAtual;
	}
	/**
	 * Retorna o nome abreviado de um mes dado seu numero $mes de 1 ate 12 (Janeiro ate Dezembro)
	 * @param int $mes
	 * @return string
	 */
	static public function getMesNomeAbreviado($mes) {
		$meses = array('Jan', 'Fev', 'Mar', 'Abril', 'Maio', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
		if ($mes > 12 || $mes < 1)
			return '';
		$mesAtual = $meses[$mes - 1];
		return $mesAtual;
	}
	/**
	 * @deprecated deu lugar a getMesNome
	 * @param int $mes
	 * @return string
	 */
	static public function transformarMes($mes) {
		return self::getMesNome($mes);
	}

	/**
	 * @deprecated deu lugar a classe FileUploader
	 */
	static public function uploadArquivo($caminho = "", $File) {
		if (!empty($File["name"])) {
			$dirUpload = PATH_ROOT . "" . $caminho . "/videos";
			//verifica se existe o caminho
			if (!file_exists("$dirUpload")) {
				mkdir("$dirUpload", 0777);

			}
			if (!preg_match("/^video\/(flv|mp4|ogg)$/", $File["type"])) {
				return json_encode(array("erro" => "Formato n&atilde;o permitido"));
			}
			preg_match("/\.(flv|mp4|ogg){1}$/i", $File["name"], $ext);
			$nome_imagem = md5(uniqid(time())) . "." . $ext[1];
			$caminho_imagem = $dirUpload . "/" . $nome_imagem;
			//mover arquivo
			$move = move_uploaded_file($File["tmp_name"], $caminho_imagem);
			if ($move) {
				return $caminho_imagem;
			} else {
				return json_encode(array("erro" => "N&atilde;o foi poss&iacute;vel carregar o v&iacute;deo!"));
			}
		}
	}

	/**
	 * Inicializa uma variavel caso ela ainda nao exista
	 * @param mixed &$var : variavel a ser inicializada
	 * @param mixed $val : valor inicial
	 */
	static public function initVar(&$var, $val = null) {
		if (!isset($var))
			$var = $val;
	}

	/**
	 *  Faz o decode de uma string que esta com charset UTF-8, caso contrário nada ocorre.
	 *  @param string &$s
	 */
	static public function utf8_decode($s) {
		if (((bool) preg_match('//u', $s)))
			$s = utf8_decode($s);
		return $s;
	}

	/**
	 *  Faz o encode de uma string que para charset UTF-8, caso ja nao esteja
	 *  @param string &$s
	 */
	static public function utf8_encode(&$s) {
		if (!((bool) preg_match('//u', $s)))
			$s = utf8_encode($s);
		return $s;
	}

	/**
	 *  Remove acentuação de uma string. Transforma a string $s para utf-8
	 *  @example:
	 *  	$s="Fumação"
	 *      Common::removerAcentuacao($s);
	 *      echo $s; -> Fumacao
	 *  @param string &$s
	 */
	static public function removerAcentuacao($s) {
		$textoRet = "";
		for ($i = 0, $x = strlen($s); $i < $x; $i++) {
			$src = utf8_decode("ÀÁÂÃÄÅÇÈÉÊËÌÍÎĨÑÒÓÔÕÖØÙÚÛÜÞßàáâãäåçèéêëìíîïðñòóôõöøüùúûýýþÿŔŕ");
			$rpc = utf8_decode("AAAAAACEEEEIIIINOOOOOOUUUUbsaaaaaaceeeeiiiidnoooooouuuuyybyRr");
			$textoRet .= strtr($s[$i], $src, $rpc);
		}
		$textoRet = str_replace("'", "", $textoRet);
		$textoRet = str_replace('"', "", $textoRet);
		return $textoRet;
	}

	/**
	 * Converte uma string com acentos para o padr�o UTF-8 (ex: � = &aacute)
	 *
	 * @param String $string
	 *        	: Texto a ser convertido
	 * @return String : Texto ja convertido
	 */
	static public function codificarHtml($string) {
		$letras = array("�" => "&aacute", "�" => "&Aacute", "�" => "&atilde", "�" => "&Atilde", "�" => "&agrave", "�" => "&Agrave", "�" => "&eacute", "�" => "&Eacute", "�" => "&iacute", "�" => "&Iacute", "�" => "&oacute", "�" => "&Oacute", "�" => "&otilde", "�" => "&Otilde", "�" => "&uacute", "�" => "&Uacute", "�" => "&ccedil", "�" => "&Ccedil");
		for ($count = 0; $count < strlen($string); $count++) {
			$letraAtual = substr($string, $count, 1);

			if (array_key_exists($letraAtual, $letras))
				$string = str_replace($letraAtual, $letras[$letraAtual], $string);
		}
		return $string;
	}

	static function stripPreposition($texto) {
		return str_replace(array(' E ', ' DE ', ' DA ', ' DO ', ' DAS ', ' DOS ', ' NA ', ' NA ', ' NAS ', ' NOS ', ' A ', ' O ', ' EM ', ' PARA ', ' E ', ' De ', ' Da ', ' Do ', ' Das ', ' Dos ', ' Na ', ' No ', ' Nas ', ' Nos ', ' A ', ' O ', ' Em ', ' Para ', ' e ', ' de ', ' da ', ' do ', ' das ', ' dos ', ' na ', ' no ', ' nas ', ' nos ', ' a ', ' o ', ' em ', ' para '), ' ', $texto);
	}

	static function removerAcentos3($texto) {
		for ($i = 0, $x = strlen($texto); $i < $x; $i++) {
			$src = "ÀÁÂÃÄÅÇÈÉÊËÌÍÎĨÑÒÓÔÕÖØÙÚÛÜÞßàáâãäåçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ";
			$rpc = "AAAAAACEEEEIIIINOOOOOOUUUUbsaaaaaaceeeeiiiidnoooooouuuyybyRr";
			$letter = strtr($texto[$i], $src, $rpc);
			$textoRet .= $letter;
		}
		return $textoRet;
	}

	/**
	 * Adiciona \ em caracteres como ' " NULL e \
	 * Aceita string, array ou ArrayObj como parâmetro
	 * @param mixed <string,array,ArrayObj>  $a
	 * @return string|ArrayObj
	 */
	public static function addslashes($a) {
		if (is_string($a))
			return addslashes($a);
		if (is_array($a))
			$a = new ArrayObj($a);
		if (is_a($a, "ArrayObj")) {
			foreach ($a as $k => $v) {
				$a -> offsetSet($k, addslashes($v));
			}
			return $a;
		}
	}
	
	/**
	 * Compara a variavel na for inteira e string.
	 * Se forem a mesma, além de retornar true, significa que $a="213".
	 * Retornará false no caso $a="213a" (ou seja, se fosse int $a valeria "213", e string vale "213a")
	 * @param string $var
	 * @return boolean
	 */
	public static function isTheSameVarIntString($var){
		return (strlen($var)===strlen(strval(intval($var))) && ord(intval($var))==ord($var));
	}
	/**
	 * Copia um arquivo $infile para outro $outfile através da web
	 * @param string $infile
	 * @param string $outfile
	 * @param boolean $progress : caso true...haverá o print do progresso em % 
	 * @return int : total de bytes copiados
	 */
	static public function copyfile_chunked($infile, $outfile, $progress=false) {
	    $chunksize = 10 * (1024); // 10 K
	    /**
	     * parse_url breaks a part a URL into it's parts, i.e. host, path,
	     * query string, etc.
	     */
	    $parts = parse_url($infile);
	    $i_handle = fsockopen($parts['host'], 80, $errstr, $errcode, 5);
	    $o_handle = fopen($outfile, 'wb');
	
	    if ($i_handle == false || $o_handle == false) {
	        return false;
	    }
	
	    if (!empty($parts['query'])) {
	        $parts['path'] .= '?' . $parts['query'];
	    }
	
	    /**
	     * Send the request to the server for the file
	     */
	    $request = "GET {$parts['path']} HTTP/1.1\r\n";
	    $request .= "Host: {$parts['host']}\r\n";
	    $request .= "User-Agent: Mozilla/5.0\r\n";
	    $request .= "Keep-Alive: 115\r\n";
	    $request .= "Connection: keep-alive\r\n\r\n";
	    fwrite($i_handle, $request);
	
	    /**
	     * Now read the headers from the remote server. We'll need
	     * to get the content length.
	     */
	    $headers = array();
	    while(!feof($i_handle)) {
	        $line = fgets($i_handle);
	        if ($line == "\r\n") break;
	        $headers[] = $line;
	    }
	
	    /**
	     * Look for the Content-Length header, and get the size
	     * of the remote file.
	     */
	    $length = 0;
	    foreach($headers as $header) {
	        if (stripos($header, 'Content-Length:') === 0) {
	            $length = (int)str_replace('Content-Length: ', '', $header);
	            break;
	        }
	    }
	
	    /**
	     * Start reading in the remote file, and writing it to the
	     * local file one chunk at a time.
	     */
	    $cnt = 0;
	    while(!feof($i_handle)) {
	        $buf = '';
	        $buf = fread($i_handle, $chunksize);
	        $bytes = fwrite($o_handle, $buf);
	        if ($bytes == false) {
	            return false;
	        }
	        $cnt += $bytes;
	        if($progress){
	        	$percent=floatval($cnt*100.00)/floatval($length);
	        	echo "\r";
	        	echo "Arquivo ".$infile."".str_repeat(".", $multiplier)." ";
	        	echo number_format(round($percent,2),2);
	        	echo "% concluído. ";
	        	ob_flush();
	        	flush();
	        }
	        
	
	        /**
	         * We're done reading when we've reached the conent length
	         */
	        if ($cnt >= $length) break;
	    }
	
	    fclose($i_handle);
	    fclose($o_handle);
	    return $cnt;
	}
}
?>
