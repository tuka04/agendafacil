<?php
/**
 * Estrutura de dados tipo Array
 * @version 1.1
 * @package classes
 * @see Exception, ArrayObject
 * @example
 * Para criar um objeto basta fazer $obj = new ArrayObj();
 * Para acessar seus métodos basta, $obj->metodo();
 * @name ArrayObj
 */
class ArrayObj extends ArrayObject {
	const __CLASS__NAME__ = __CLASS__;
	/**
	 * Constroi objeto array
	 * @param array $array
	 */
	public function __construct($array=array()){
		parent::__construct($array);
	}
	
	/**
	 * Busca um registro do array pelo seu valor, retorna a key do valor ou null caso nao encontre
	 * Busca tbm em subarrays ou subobjetos (do tipo ArrayObj)
	 * @version 1.1
	 * @param mixed $valor
	 * @return mixed $key
	 */
	public function search($valor){
		foreach ($this->getArrayCopy() as $k=>$v){
			if(is_array($v)){
				foreach ($v as $v1){
					if(is_array($v1))
						$aux=new ArrayObj($v1);
					if(is_a($v1,__CLASS__))
						$aux=$v1;
					if(isset($aux) && is_a($aux,__CLASS__))
						if(!is_null($aux->search($valor)))
							return $aux->search($valor);
					unset($aux);
					if($v1 == $valor)
						return $k;
				}
			}
			else if(is_object($v) && method_exists($v, "getArrayCopy")){
				foreach ($v->getArrayCopy() as $v1){
					if(is_array($v1))
						$aux=new ArrayObj($v1);
					if(is_a($v1,__CLASS__))
						$aux=$v1;
					if(isset($aux) && is_a($aux,__CLASS__))
						if(!is_null($aux->search($valor)))
							return $aux->search($valor);
					unset($aux);
					if($v1 == $valor)
						return $k;
				}
			}
			else if($v == $valor)
				return $k;
		}
		return null;
	}
	/**
	 * @param string $glue
	 * @return string
	 */
	public function toString($glue=","){
		$str = "";
		foreach ($this->getArrayCopy() as $a){
			if(is_object($a) && method_exists($a, "toString"))
				$str .= $a->toString().$glue;
			elseif(is_array($a))
			$str .= implode($glue,$a).$glue;
			else
				$str .= $a.$glue;
		}
		return rtrim($str,$glue);
	}
	
	/**
	 * Remove os valores $v, presentes nesse array
	 * @param mixed $v
	 */
	public function removeValue($v){
		$aux = new ArrayObject();
		$removed=false;
		foreach ($this->getArrayCopy() as $k=>$a){
			if($a!=$v || $removed)
				$aux->offsetSet($k,$a);
			else{
				$removed=true;
			}
		}
		$this->exchangeArray($aux->getArrayCopy());
	}
	/**
	 * Remove um valor e o retorna dada sua key.
	 * Retorna null caso nao encontre
	 * @param mixed $key
	 */
	public function removeByKey($key){
		$ret=null;
		if($this->offsetExists($key)){
			$aux = new ArrayObject();
			foreach ($this->getArrayCopy() as $k=>$a){
				if($k!=$key)
					$aux->offsetSet($k,$a);
				else
					$ret=$a;
			}
			$this->exchangeArray($aux->getArrayCopy());	
		}
		return $ret;
	}
	/**
	 * converte esse array em json
	 * @return json
	 */
	public function toJson(){
		return json_encode($this->getArrayCopy());
	}
	
	/**
	 * Filtra, remove, as posicoes em branco de um array
	 */
	public function filterEmpty(){
		$this->removeValue("");
	}
	/**
	 * Faz a troca de um valor por outro em todas as posicoes em que $val for igual as valor da uma posicao. Faz deepsearch, em caso de ArrayObj como valor
	 * @param mixed $val
	 * @param int $key
	 */
	public function replace($val,$replace){
		$aux = new ArrayObj();
		foreach ($this as &$v){
			if(empty($v)&&empty($val))
				$v=$replace;
			else if($v==$val)
				$v=$replace;
			if(is_a($v,self::__CLASS__NAME__))
				$v->replace($val,$replace);
		}
	}
	/**
	 * Perform hash com merge dos campos, onde o campo torna-se a offset
	 * Levanta Exception em caso de alguma falha
	 * @param string $offset
	 * @version 1.1
	 * @see Exception
	 */
	public function exchangeToHash($offset,$transformToObj=false){
		$ret = new ArrayObject();
		$r = $this->getArrayCopy();
		if(isset($r[$offset]))
			$r=array($r);
		foreach ($r as $a){
			if(!is_array($a) && !is_object($a))
				continue;
			if(is_object($a)){
				if(!is_a($a,self::__CLASS__NAME__))
					throw new Exception("Objeto não é do tipo ArrayObj");
				$a=$a->getArrayCopy();
			}
			if(!array_key_exists($offset, $a)){
				continue;
			}
			/*$a[$offeset] em branco ou nulo causa warning e mal comportamento do hash*/
			if(trim($a[$offset])=="" || $a[$offset]=="" || is_null($a[$offset]))
				continue;
			if(!$ret->offsetExists($a[$offset])){
				$ins=$a;
				if($transformToObj){
					if(is_array($ins))
						$ins=new ArrayObj(array($ins));
					if(!is_a($ins,__CLASS__))
						$ins=new ArrayObj(array(new ArrayObj(array($ins))));
				}
				$ret->offsetSet($a[$offset],$ins);
			}
			else{
				$aux = $ret->offsetGet($a[$offset]);
				if(is_object($aux) && method_exists($aux, __FUNCTION__)){
					$aux->append($a);
				}
				else{
					$auxArray = new ArrayObj();
					$auxArray->append($aux);
					$auxArray->append($a);
					$aux = $auxArray;
				}
				$ret->offsetSet($a[$offset], $aux);
			}
		}
		$this->exchangeArray($ret->getArrayCopy());
	}
	
	/**
	 * 
	 * verifica se o Array nao esta null
	 * @return boolean
	 */
	public function isNull(){
		return ($this->count() > 0);
	}
	/**
	 * Retorna uma string no estilo post key1=val1&key2=val2
	 * @return string
	 */
	public function toPOSTstr(){
		$str = "";
		$glue="&";
		foreach ($this->getArrayCopy() as $k=>$a){
			if(is_object($a) && method_exists($a, "toPOSTstr"))
				$str .= $a->toPOSTstr().$glue;
			elseif(is_array($a))
				foreach ($a as $k=>$v)
					$str .= $k."=".$v.$glue;
			else
				$str .= $k."=".$a.$glue;
		}
		return rtrim($str,$glue);
	}
	/**
	 * Retorna um ArrayObj com todas as keys do array
	 * @return ArrayObj
	 */
	public function getArrayKey(){
		return new ArrayObj(array_keys($this->getArrayCopy()));
	}
	/**
	 * executa utf8_encode em todos os elementos, string, dessa estrutura. 
	 * Caso encontre algum ArrayObj na subestrutura, ele executa recursivamente.
	 */
	public function utf8_encode(){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_string($v))
				$v=Common::utf8_encode($v);
			else if(is_a($v,self::__CLASS__NAME__))
				$v->utf8_encode();
		}
	}
	
	/**
	* executa utf8_decode em todos os elementos, string, dessa estrutura.
	* Caso encontre algum ArrayObj na subestrutura, ele executa recursivamente.
	*/
	public function utf8_decode(){
    	foreach ($this as &$v){
        	if(is_array($v))
            	$v=new ArrayObj($v);
            if(is_string($v))
            	$v=Common::utf8_decode($v);
            else if(is_a($v,self::__CLASS__NAME__))
            	$v->utf8_decode();
        }
    }
	/**
	 * executa html_entity_decode em todos os elementos, string, dessa estrutura. 
	 * Caso encontre algum ArrayObj na subestrutura, ele executa recursivamente.
	 */
	public function html_entity_decode(){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_string($v))
				$v=html_entity_decode($v,ENT_QUOTES,"UTF-8");
			else if(is_a($v,self::__CLASS__NAME__))
				$v->html_entity_decode();
		}
	}
	/**
	 * executa htmlentities em todos os elementos, string, dessa estrutura.
	 * Caso encontre algum ArrayObj na subestrutura, ele executa recursivamente.
	 */
	public function htmlentities(){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_string($v))
				$v=htmlentities($v,ENT_QUOTES,"UTF-8");
			else if(is_a($v,self::__CLASS__NAME__))
				$v->htmlentities();
		}
	}
	/**
	 * Executa o strip_tags($str,$allowable_tags);
	 * @param string $allowed_tags
	 * @see http://php.net/manual/pt_BR/function.strip-tags.php
	 */
	public function strip_tags($allowed_tags=null){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_string($v))
				$v=strip_tags($v,$allowed_tags);
			else if(is_a($v,self::__CLASS__NAME__))
				$v->strip_tags($allowed_tags);
		}
	}
	/**
	 * executa stripcslashes em todos os elementos, string, dessa estrutura.
	 * Caso encontre algum ArrayObj na subestrutura, ele executa recursivamente.
	 */
	public function stripcslashes(){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_string($v))
				$v=stripcslashes($v);
			else if(is_a($v,self::__CLASS__NAME__))
				$v->stripcslashes();
		}
	}
	/**
	 * executa stripcslashes em todos os elementos, string, dessa estrutura.
	 * Caso encontre algum ArrayObj na subestrutura, ele executa recursivamente.
	 */
	public function stripslashes(){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_string($v))
				$v=stripslashes($v);
			else if(is_a($v,self::__CLASS__NAME__))
				$v->stripslashes();
		}
	}
	/**
	 * executa stripcslashes em todos os elementos, string, dessa estrutura.
	 * Caso encontre algum ArrayObj na subestrutura, ele executa recursivamente.
	 */
	public function addslashes(){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_string($v))
				$v=addslashes($v);
			else if(is_a($v,self::__CLASS__NAME__))
				$v->addslashes();
		}
	}
	/**
	 * Transforma todos os elementos que são array em ArrayObj
	 */
	public function arrayToArrayObj(){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_a($v,self::__CLASS__NAME__))
				$v->arrayToArrayObj();
		}
	}
	
	public function concat(ArrayObj $arr){
		foreach ($arr as $k=>$v){
			if(!$this->offsetExists($k))
				$this->offsetSet($k, $v);
			else
				$this->append($v);
		}
	}
	
	/**
	 * Função auxiliar de $this->permute();
	 * @param array $p
	 * @return boolean|array
	 */
	private function permute_next($p,$size){
		// slide down the array looking for where we're smaller than the next guy
		for ($i = $size - 1; isset($p[$i]) && $p[$i] >= $p[$i+1]; --$i) { }
		
		// if this doesn't occur, we've finished our permutations
		// the array is reversed: (1, 2, 3, 4) => (4, 3, 2, 1)
		if ($i == -1) { return false; }
		
		// slide down the array looking for a bigger number than what we found before
		for ($j = $size; $p[$j] <= $p[$i]; --$j) { }
		
		// swap them
		$tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;
		
		// now reverse the elements in between by swapping the ends
		for (++$i, $j = $size; $i < $j; ++$i, --$j) {
			$tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;
		}
		
		return $p;
		
	}
	/**
	 * Função que faz a permutação dos elementos desse Objeto
	 * @see pc_next_permutation() http://docstore.mik.ua/orelly/webprog/pcook/ch04_26.htm
	 * @return ArrayObj
	 */
	public function permute(){
		$set = $this->getArrayCopy();
		$size = count($set) - 1;
		$perm = range(0, $size);
		$j = 0;
		do {
			foreach ($perm as $i) { 
				$perms[$j][]=$set[$i];
			 }
		} while ($perm = $this->permute_next($perm, $size) and ++$j);
		$this->exchangeArray($perms);
	}
	/**
	 * Transforma todos os valores em intval
	 */
	public function intval(){
		foreach ($this as &$v){
			if(is_array($v))
				$v=new ArrayObj($v);
			if(is_a($v,self::__CLASS__NAME__))
				$v->intval();
			$v=intval($v);
		}
	}
	/**
	 * Faz o print_r desse array
	 * @param boolean $die
	 */
	public function debug($die=true){
		echo "<PRE>";
		print_r($this);
		echo "</PRE>";
		if($die)
			die("fim debug");
	}
	
}

?>
