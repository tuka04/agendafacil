<?php
class MySQLExtra {
	
	/**
	 * Lista com os campos que irão na query
	 * @var ArrayObj
	 */
	private $campo;
	/**
	 * Lista com os joins
	 * @var ArrayObj $join
	 */
	private $join;
	/**
	 * Lista com os agrupamentos
	 * @var ArrayObj
	 */
	private $group;
	/**
	 * Lista com os campos ordenadores
	 * @var ArrayObj
	 */
	private $order;
	/**
	 * LIMIT limit_start,limit_count
	 * @var int $limit_start
	 */
	private $limit_start;
	/**
	 * LIMIT limit_start,limit_count
	 * @var int $limit_count
	 */
	private $limit_count;
	
	private $where_type="AND";
	
	private $where_like="%";
	
	public function __construct(){
		$this->campo=new ArrayObj();
		$this->join=new ArrayObj();
		$this->group=new ArrayObj();
		$this->order=new ArrayObj();
		$this->limit_start=null;
		$this->limit_count=null;
	}
	
	public function getSearchType(){
		return $this->where_type;
	}
	
	public function getSearchLike(){
		return $this->where_like;
	}
	
	public function setSearchEqual(){
		$this->where_like="";
	}
	
	public function setSearchLike(){
		$this->where_like="%";
	}
	
	public function setSearchAND(){
		$this->where_type="AND";
	}
	
	public function setSearchOR(){
		$this->where_type="OR";
	}
	
	
	public function setLimit($limit_start,$limit_count){
		Validator::inteiro($limit_start);
		Validator::inteiro($limit_end);
		
		$this->limit_start=$limit_start;
		$this->limit_count=$limit_count;
	}
	
	public function addCampo($campo){
		if(empty($campo)||$campo=="")
			return false;
		$this->campo->append($campo);
	}
	
	public function addJoin($join){
		if(empty($join)||$join=="")
			return false;
		$this->join->append($join);
	}
	
	public function addGroup($group){
		if(empty($group)||$group=="")
			return false;
		$this->group->append($group);
	}
	
	public function addOrder($order){
		if(empty($order)||$order=="")
			return false;
		$this->order->append($order);
	}
	
	public function getNumCampo(){
		return $this->campo->count();
	}
	
	public function campoToString(){
		return $this->campo->toString();
	}
	public function joinToString(){
		return $this->join->toString(" ");
	}
	
	public function groupToString(){
		if($this->group->count()==0)
			return "";
		return " GROUP BY ".$this->group->toString();
	}
	
	public function limitToString(){
		if(is_null($this->limit_start) || $this->limit_start<0)
			return "";
		if(is_null($this->limit_count) || $this->limit_count<0)
			return "";
		return " LIMIT ".$this->limit_start.",".$this->limit_count." ";
	}
	
	public function orderToString(){
		if($this->order->count()==0)
			return "";
		return " ORDER BY ".$this->order->toString();
	}
	/**
	 * Retorna o esquema join + where + group + order + limit 
	 * @param string $where
	 */
	public function toString($where=""){
		return $this->joinToString().
			   $where.
			   $this->groupToString().
		       $this->orderToString().
		       $this->limitToString();
	}
}