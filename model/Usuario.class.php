<?php

class Usuario extends Model {
	
	const TABLE="usuario";
	
	public function __construct(){
		parent::__construct(self::TABLE);
	}
	/**
	 * Insere um novo usuario, verificando se o email já existe
	 * {@inheritDoc}
	 * @see Model::post()
	 * @throws ValidatorException
	 * @return int
	 */
	public function post(ArrayObj $vars){
		Validator::email($vars->offsetGet("email"));
		Validator::conjuntoNaoVazio($vars);
		
		$email=new ArrayObj(array("email"=>$vars->offsetGet("email")));

		if($this->get($email)->count()>0)
			throw new ValidatorException(ValidatorException::EMAIL_CADASTRADO);
		return parent::post($vars);
	}
	
	public function put(ArrayObj $vars){
		if($vars->offsetExists("email"))
			Validator::email($vars->offsetGet("email"));		
		Validator::conjuntoNaoVazio($vars);
		
		parent::put($vars);
	}
	
	
}
?>