<?php

require_once PATH_ROOT.'model/Usuario.class.php';
class Login extends Usuario {
	
	const SESS_ID="uid";
	const SESS_EMAIL="uem";
	const SESS_EUSOU="ues";
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Função que realiza a autenticação do usuário;
	 * @param ArrayObj $vars
	 */
	public function get(ArrayObj $vars){
		Validator::email($vars->offsetGet("email"));
		Validator::obrigatorio($vars->offsetGet("senha"));
		
		require_once PATH_ROOT.'model/UsuarioEusou.class.php';
		
		$this->setTableAlias("usr");//table alias
		$extra=new MySQLExtra();
		$extra->addCampo("usr.email as email, usr.id as id, e.id as eusou");
		$extra->addJoin("INNER JOIN ".UsuarioEusou::TABLE." as e ON e.id=usr.usuarioEusouID ");
		
		$r=parent::get($vars,$extra);
		
		if($r->count()!=1)
			throw new LogicException(LoginException::DADOS_INVALIDOS);
		
		$r=new ArrayObj($r[0]);
		$this->registraSessao($r);
		return $r;
		
	}
		
	private function registraSessao(ArrayObj $vars){
		if(!$vars->offsetExists("id"))
			throw new LoginException(LoginException::ERRO_CARREGAR_DADOS);
		if(!$vars->offsetExists("email"))
			throw new LoginException(LoginException::ERRO_CARREGAR_DADOS);
		if(!$vars->offsetExists("eusou"))
			throw new LoginException(LoginException::ERRO_CARREGAR_DADOS);
		$_SESSION[self::SESS_ID]=intval($vars["id"]);
		$_SESSION[self::SESS_EMAIL]=$vars["email"];
		$_SESSION[self::SESS_EUSOU]=intval($vars["eusou"]);
	}
	/**
	 * Logout
	 * {@inheritDoc}
	 * @see Model::delete()
	 */
	public function delete(ArrayObj $vars){
		session_destroy();
	}
}


class LoginException extends RuntimeException {
	const DADOS_INVALIDOS="Os dados fornecidos estão inválidos.";
	const ERRO_CARREGAR_DADOS="Erro ao carregar dados.";
}

?>
