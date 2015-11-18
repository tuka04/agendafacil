<?php

class EspecialidadeUsuario extends Model {
	
	const TABLE="especialidade_usuario";
	
	public function __construct(){
		parent::__construct(self::TABLE);
	}
}

?>