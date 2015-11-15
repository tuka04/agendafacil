<?php

abstract class Model {
	
	private $table=null;
	
	public function __construct($table){
		$this->setTable($table);
	}
	
	public function put($vars){
		MySQL::execute("REPLACE INTO ".$this->getTable()." (".$this->getFields()->toString().") VALUES (".$this->getValues()->toString().");");
	}
	
	public function post($vars){
		$this->put($vars);
	}
	
	public function delete($vars){
		MySQL::execute("DELETE FROM ".$this->getTable()." WHERE id IN (".$this->getValues()->toString().");");
	}
	
	public function get($vars){
		$fields=$this->getFields();
		if($fields->count()==0)
			return MySQL::execute("SELECT * FROM ".$this->getTable().";");
		
		$values=$this->getValues();
		
		$type=" AND ";
		for($i=0;$i<$fields->count();$i++){
			if(is_int($values[$i]))
				$search.=" ".$fields[$i]."=".$values[$i].$type;
			else
				$search.=" ".$fields[$i]."LIKE '%".$values[$i]."%'".$type;
		}
		$search=substr($search,0,-1*(strlen($type)));
		
		return MySQL::execute("SELECT * FROM ".$this->getTable()." WHERE ".$search.";");
	}
	
	public function setTable($table){
		if($table==""||empty($table)||is_null($table))
			throw new ErrorException("Tabela inválida");
		$this->table=$table;
	}
	
	public function getTable(){
		return $this->table;
	}
	/**
	 * Retorna os valores para inserção/edição/deleção
	 * @return ArrayObj
	 */
	private function getValues(){
		$values=new ArrayObj();
		foreach ($vars as $v){
			$v=addslashes($v);
			if(Common::isTheSameVarIntString($v))
				$values->append(intval($v));
			else 
				$values->append("'".$v."'");
		}
		return $values;
	}
	/**
	 * Retorna os campos para inserção/edição
	 * @return ArrayObj
	 */
	private function getFields(){
		$fields=new ArrayObj();
		foreach ($vars as $k=>$v) 
			$fields->append(addslashes($k));
		return $fields;
	}
}

?>