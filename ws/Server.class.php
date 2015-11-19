<?php
/**
 * Classe que trata a variavel $_SERVER
 */

abstract class Server {
	
	/**
	 * Retorna $_SERVER["REQUEST_URI"]
	 * @return string
	 */
	static public function getURI(){
		return $_SERVER["REQUEST_URI"];
	}
	

	/**
	 * Retorna $_SERVER["REQUEST_METHOD"]
	 * @return string
	 */
	static public function getMethod(){
		return strtoupper($_SERVER["REQUEST_METHOD"]);
	}
	/**
	 * @return array
	 */
	static public function getURIPath(){
		$uri=parse_url(self::getURI());
		$paths = explode('/',$uri['path']);
		if(isset($uri["query"]) && $uri["query"]!="" && $paths[3]=="")
			$paths[3]=urldecode($uri["query"]);
		array_shift($paths); //remove o primeiro elemento que é vazio
		return $paths;
	}
	/**
	 * 
	 * @param  array $paths
	 * @throws ErrorException
	 * @return string|null
	 */
	static public function getResource($paths){
		if(!is_array($paths))
			throw new ErrorException("Variável deverá ser um array");
		return isset($paths[1])?$paths[1]:null;
	}
	/**
	 * 
	 * @param  array $paths
	 * @throws ErrorException
	 * @return ArrayObj
	 */
	static public function getVars($paths){
		if(!is_array($paths))
			throw new ErrorException("Variável deverá ser um array");
		if(!isset($paths[2])||empty($paths[2])||trim($paths[2])==""){
			$str=file_get_contents('php://input');
			if(!empty($str)||$str!="")
				return new ArrayObj(get_object_vars(json_decode($str)));
			return new ArrayObj();
		}
		return new ArrayObj(get_object_vars(json_decode($paths[2])));
	}
}