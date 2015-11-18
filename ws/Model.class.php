<?php

abstract class Model {
	/**
	 * @var string $table
	 */
	private $table=null;
	private $table_alias=null;
	public function __construct($table){
		$this->setTable($table);
		$this->setTableAlias($table);
	}
	/**
	 * @param ArrayObj $vars
	 * @return ArrayObj
	 */
	public function put(ArrayObj $vars){
		MySQL::execute("UPDATE ".$this->getTable()." SET ".$this->getUpdateClause($vars)->toString()." WHERE id=".$this->getObjectID($vars).";");
	}
	/**
	 * 
	 * @param ArrayObj $vars
	 * @return ArrayObj
	 * @return int
	 */
	public function post(ArrayObj $vars){
		return MySQL::execute("INSERT INTO ".$this->getTable()." (".$this->getFields($vars)->toString().") VALUES (".$this->getValues($vars)->toString().");");
	}
	/**
	 * @param ArrayObj $vars
	 * @return ArrayObj
	 */
	public function delete(ArrayObj $vars){
		MySQL::execute("DELETE FROM ".$this->getTable()." WHERE id IN (".$this->getValues($vars)->toString().");");
	}
	/**
	 * @param ArrayObj $vars
	 * @return ArrayObj
	 */
	public function get(ArrayObj $vars, MySQLExtra $extra=null){
		if(is_null($extra))
			$extra=new MySQLExtra();
				
		$type=$extra->getSearchType();
		$like=$extra->getSearchLike();
		$tableAlias=" as ".$this->getTableAlias()." ";
		$campoStr=" ".$this->getTableAlias().".*";
		if($extra->getNumCampo()>0)
			$campoStr=$extra->campoToString();
		
		
		$fields=$this->getFields($vars);
		if($fields->count()==0)//há campos para busca? se 0 entao nao	
			return MySQL::execute("SELECT ".$campoStr." FROM ".$this->getTable().$tableAlias.$extra->toString().";");
		
		$values=$this->getValues($vars,$like);//se like nao for %, então a busca será field LIKE 'value' ao invez de field LIKE '%value%'
		
		$search="";
		for($i=0;$i<$fields->count();$i++){
			if(is_int($values[$i]))
				$search.=" ".$fields[$i]."=".$values[$i].$type;
			else
				$search.=" ".$fields[$i]." LIKE ".$values[$i]." ".$type;
		}
		$search=substr($search,0,-1*(strlen($type)));
		
		return MySQL::execute("SELECT ".$campoStr." FROM ".$this->getTable().$tableAlias.$extra->toString(" WHERE ".$search).";");
	}
	/**
	 * @param string $table
	 * @throws ErrorException
	 */
	public function setTableAlias($table_alias){
		if($table_alias==""||empty($table_alias)||is_null($table_alias))
			throw new ErrorException("Tabela inválida");
			$this->table_alias=$table_alias;
	}
	/**
	 * @return string
	 */
	public function getTableAlias(){
		return $this->table_alias;
	}
	/**
	 * @param string $table
	 * @throws ErrorException
	 */
	public function setTable($table){
		if($table==""||empty($table)||is_null($table))
			throw new ErrorException("Tabela inválida");
		$this->table=$table;
	}
	/**
	 * @return string
	 */
	public function getTable(){
		return $this->table;
	}
	/**
	 * Retorna os valores para inserção/edição/deleção
	 * @param ArrayObj $vars
	 * @param string $concat : string de concatenação com valor...ex: "'".$concat.$v.$concat."'"; se $concat="%" então, '%$v%';
	 * @return ArrayObj
	 */
	public function getValues(ArrayObj $vars,$concat=""){
		$values=new ArrayObj();
		foreach ($vars as $v){
			$v=addslashes($v);
			if(Common::isTheSameVarIntString($v))
				$values->append(intval($v));
			else 
				$values->append("'".$concat.$v.$concat."'");
		}
		return $values;
	}
	/**
	 * Retorna os campos para inserção/edição
	 * @param ArrayObj $vars
	 * @return ArrayObj
	 */
	public function getFields(ArrayObj $vars){
		$fields=new ArrayObj();
		foreach ($vars as $k=>$v) 
			$fields->append(addslashes($k));
		return $fields;
	}
	/**
	 * Retorna query para update clause do mysql
	 * @param ArrayObj $vars
	 * @return ArrayObj
	 */
	public function getUpdateClause(ArrayObj $vars){
		$update=new ArrayObj();
		foreach ($vars as $k=>$v){
			$v=addslashes($v);
			$k=addslashes($k);
			if(Common::isTheSameVarIntString($v))
				$update->append($k."=".intval($v));
			else
				$update->append($k."='".$v."'");
		}
		if($update->count()==0)
			throw new RuntimeException("Não há dados para editar.");
		return $update;
	}
	/**
	 * @param ArrayObj $vars
	 * @throws RuntimeException
	 * @return int
	 */
	public function getObjectID(ArrayObj $vars){
		if(!$vars->offsetExists("id"))
			throw new RuntimeException('ID está faltando. Enviar como o exemplo: (json){"id":"1"}');
		return intval($vars->offsetGet("id"));
	}
}

?>