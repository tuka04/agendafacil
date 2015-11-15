<?php

class Usuario extends Model {
	
	const TABLE="usuario";
	
	public function __construct(){
		parent::__construct(self::TABLE);
	}
}
?>