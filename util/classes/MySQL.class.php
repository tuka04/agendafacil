<?php
/**
 * Classe que manipula acesso e query a banco de dado
 * @see PDO
 * @package classes
 * @version 1.3
 * @name MySQL
 * @example $m = new MySQL();
 */

class MySQL extends PDO {
	
	/**
	 * Constroi conexao
	 */ 
	public function __construct(){
		$dns="mysql:dbname=".DB_BASE.";host=".DB_HOST;
		try{
			parent::__construct($dns, DB_USER, DB_PASS);
		} catch (PDOException $e){
			HTTPHeader::set(HTTPResponseCode::INTERNAL_SERVER_ERROR);
		}
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	/**
	 * Executa uma query dado $qr
	 * @param string $qr
	 * @param boolean $errordie : para execucao do script em caso de erro , se for false retorna false
	 * @return ArrayObj|boolean|int
	 * @example O retorno depende da query, se for inser retorn int, delete boolean, update boolean, select ArrayObj
	 * @see PDO
	 */
	public function execQuery($qr="",$errordie=true){
		$insert=false;
		$select=false;
		if(stripos($qr,"INSERT INTO")!==false && stripos(ltrim($qr),"INSERT INTO")==0)
			$insert=true;
		else if((stripos($qr, "SELECT ")!==false && stripos(ltrim($qr),"SELECT ")==0) || (stripos($qr,"DESC ")!==false && stripos(ltrim($qr),"DESC ")==0) || (stripos($qr,"SHOW ")!== false && stripos(ltrim($qr),"SHOW ")==0)){
			$select=true;
		}
		$ret = new PDOStatement();
		try{
			$this->beginTransaction();
			$ret = $this->prepare($qr);			
			$ret->execute();
			if($insert)
				$id = $this->lastInsertId();
			$this->commit();
		}catch (PDOException $e){
			$this->rollBack();
			if($errordie)
				Erro::displayDie($e->getMessage().": ".__FILE__." ".__LINE__);
			return false;
		}
		if($insert)
			return $id;
		if($select){
			$retornoSelect = new ArrayObj($ret->fetchAll(PDO::FETCH_ASSOC));
			return $retornoSelect;
		}	
		return true;
	}
	static public function execute($qr){
		$insert=false;
		$select=false;
		$write=false;
		$qr=trim($qr);
		if(stripos($qr,"INSERT INTO")!==false && stripos(trim($qr),"INSERT INTO")==0){
			$insert=true;
			$write=true;
		}
		else if(stripos($qr,"UPDATE")!==false && stripos(trim($qr),"UPDATE")==0){
			$write=true;
		}
		else if((stripos($qr, "SELECT")!==false && stripos($qr, "SELECT")==0) || (stripos($qr,"DESC ")!==false && stripos(trim($qr),"DESC ")==0) || (stripos($qr,"SHOW ")!== false && stripos(trim($qr),"SHOW ")==0)){
			$select=true;
			$write=false;
		}
		$ret = new PDOStatement();
		$pdo = self::getPDO(($write?'w':'r'));
		try{
			$pdo->beginTransaction();
			$ret = $pdo->prepare($qr);
			$ret->execute();
			if($insert)
				$id = $pdo->lastInsertId();
			$pdo->commit();
		}catch (PDOException $e){
			$pdo->rollBack();
			Erro::displayDie($e->getMessage());
		}
		if($insert)
			return $id;
		if($select){
			$retornoSelect = new ArrayObj($ret->fetchAll(PDO::FETCH_ASSOC));
			return $retornoSelect;
		}
		return true;
	}
	/**
	 * Retorna um objeto do tipo PDO de acordo com o tipo de operação (leitura ou escrita)
	 * @param string $op
	 * @return PDO
	 */
	static public function getPDO($op){
		try{
			$server=MySQLServerPolice::getServerHost($op);//pega o servidor de acordo com a politica de balance implementada
			$pdo=MySQLConnections::getConnection($server);//verifica se ha conexao aberta, para esse server
			if(is_null($pdo)){//nao existe a conexao
				$dns="mysql:dbname=".DB_BASE.";host=".$server;
				$pdo=new PDO($dns, DB_USER, DB_PASS);
				MySQLConnections::addConnection($pdo, $server);//add to list
			}
		} catch (PDOException $e){
			Erro::displayDie($e->getMessage().": ".$e->getLine());
		} catch (RuntimeException $e){
			Erro::displayDie($e->getMessage().": ".$e->getLine());
		}
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $pdo;
	}
	/**
	 * @param string $tabela
	 * @param ArrayObj $dados
	 * @return int (last_inserted_id)
	 */
	static public function inserir($tabela,ArrayObj $dados){
		$qr="INSERT INTO ".$tabela." ";
		$campos="(";
		$values="(";
		foreach ($dados as $k=>$v){
			$campos.="`".$k."`,";
			if(is_int($v))
				$values.=$v.",";
			else
				$values.="'".$v."',";
		}
		$campos=substr($campos,0,-1).")";
		$values=substr($values,0,-1).")";
		$qr.=" ".$campos." VALUES ".$values;
		return MySQL::execute($qr);
	}
	/**
	 * @param ArrayObj<string> $campos
	 * @return string
	 */
	static public function getStrCampos(ArrayObj $campos=null){
		$camposstr = " * ";
		if(!is_null($campos) && $campos->count()>0)
			$camposstr = $campos->toString();
		return $camposstr;
	}
	/**
	 * @param ArrayObj<MySQLJoin> $join
	 * @throws Exception
	 * @return string
	 */
	static public function getStrJoin(ArrayObj $join=null){
		$joinStr=" ";
		if(!is_null($join)){
			require_once PATH_ROOT.'sg/conteudos/model/ConteudosCelulaAnexo.class.php';
			foreach ($join as $j){
				if(!is_a($j,MySQLJoin::__CLASS_NAME__))
					throw new Exception("A estrutura (ArrayObj) de join deverá conter apenas MySQLJoin objects");
				$joinStr.=" ".$j->toString()." ";
			}
		}
		return $joinStr;	
	}
	/**
	 * @param ArrayObj<string> $group
	 * @return String
	 */
	static public function getStrGroup(ArrayObj $group = null) {
		$groupStr = "";
		if(!is_null($group) && $group->count() > 0) {
			$groupStr = "GROUP BY ";
			$groupStr .= $group->toString();
		}
		return $groupStr;
	}
	/**
	 * @param ArrayObj<string> $group
	 * @return String
	 */
	static public function getStrOrder(ArrayObj $order = null) {
		$groupStr = "";
		if(!is_null($order) && $order->count() > 0) {
			$groupStr = "ORDER BY ";
			$groupStr .= $order->toString();
		}
		return $groupStr;
	}
}


final class MySQLServerPolice{
	/**
	 * Politica de servidores para leitura e escrita
	 * @var int
	 */
	const READ_WRITE=1;
	/**
	 * Politica selecionada
	 * @var int
	 */
	static private $police=self::READ_WRITE;
	/**
	 * Lista com servidores
	 * @var array
	 */
	static private $servers=array("w"=>DB_HOST,"r"=>DB_HOST);
	/**
	 * @param string $op
	 * @throws RuntimeException
	 * @return Ambigous <string, mixed>
	 */
	static public function getServerHost($op){
		if(self::$police==self::READ_WRITE)	
			return self::getReadWriteServer($op);
	}
	/**
	 * @param string $op
	 * @throws RuntimeException
	 * @return string
	 */
	static private function getReadWriteServer($op){
		$aux=new ArrayObj(self::$servers);
		if($aux->count()<2)
			throw new RuntimeException("Número de servidores insuficiente");
		if(!$aux->offsetExists($op))
			throw new RuntimeException("Operador inválido: para esta politica deverá ter valor: w ou r");
		return $aux->offsetGet($op);
	}
	
}
final class MySQLJoin {
	const __CLASS_NAME__="MySQLJoin";
	private $tabela;
	private $left;
	private $right;
	private $join;
	const INNER="INNER";
	const LEFT="LEFT";
	const RIGHT="RIGHT";
	const JOIN="";
	public function __construct($tabela,$left,$right,$join){
		$this->tabela=$tabela;
		$this->left=$left;
		$this->right=$right;
		$this->join=$join;
	}
	public function toString(){
		return $this->join." JOIN ".$this->tabela." ON ".$this->right." = ".$this->left." ";
	}
	/**
	 * @return string
	 */
	public function getTabela(){
		return $this->tabela;
	}
}
final class MySQLConnections {
	static private $conn=null;
	static public function addConnection(PDO $c, $server){
		if(is_null(self::$conn))
			self::$conn=new ArrayObj();
		self::$conn->offsetSet($server, $c);
	}
	/**
	 * @param string $server
	 * @return NULL|PDO
	 */
	static public function getConnection($server){
		if(is_null(self::$conn) || !self::$conn->offsetExists($server))
			return null;
		return self::$conn->offsetGet($server);
	}
}
?>
