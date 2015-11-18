<?php

class Especialidade extends Model {
	
	const TABLE="especialidade";
	
	public function __construct(){
		parent::__construct(self::TABLE);	
	}
	/**
	 * Verifica se existe o nome antes de inserir
	 * {@inheritDoc}
	 * @see Model::post()
	 */
	public function post(ArrayObj $vars){
		Validator::conjuntoNaoVazio($vars);
		if(!$vars->offsetExists("nome"))
			throw new ValidatorException(ValidatorException::NOME);
		//busca por nome. Evitar duplicatas
		$nome=new ArrayObj(array("nome"=>$vars->offsetGet("nome")));
		if($this->get($nome,null,null)->count()>0)
			throw new ValidatorException(ValidatorException::NOME_CADASTRADO);
		
		return parent::post($vars);
	}
	
	public function put(ArrayObj $vars){
		Validator::conjuntoNaoVazio($vars);
		if(!$vars->offsetExists("nome"))
			throw new ValidatorException(ValidatorException::NOME);
		parent::put($vars);
	}
}

?>