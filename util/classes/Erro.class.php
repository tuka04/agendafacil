<?php
/**
 * Classe que manipula e imprime erros
 * @package classes
 * @version 1.2
 * @package classes
 * @name Erro
 */
class Erro {
	/**
	 * Imprime um erro, sem parar a execucao do script
	 * @example Erro::display($msg) -> Imprime no formato json (error=>true, msg=>$msg)
	 * @param string $msg
	 */	
	public static function display($msg=""){
		HTTPHeader::setReponseJson();
		HTTPHeader::set(HTTPResponseCode::INTERNAL_SERVER_ERROR);
		$arr=array($msg);
		echo json_encode($arr);
	}
	
	/**
	 * Imprime um erro, parando a execucao do script
	 * @example Erro::display($msg) -> Imprime no formato json (error=>true, msg=>$msg)
	 * @param string $msg
	 */
	public static function displayDie($msg=""){
		die(self::display($msg));
	}
	/**
	 * Escreve $msg em arquivo de log
	 * @param string $fname
	 * @param string $msg
	 * @throws Exception
	 */
	public static function writeLog($fname,$msg){
		try{
			$f=new SplFileObject($fname, "w+");
			if($f==null)
				throw new Exception("Erro arbr arquivo, ".$fname." . ".__FILE__.' Linha: '.__LINE__);
			if($f->fwrite($msg)==null)
				throw new Exception("Erro ao escrever em arquivo. ".__FILE__.' Linha: '.__LINE__);
		}catch (RuntimeException $e){
			Erro::displayDie($e->getMessage());
		}
	}
	/**
	 * Imprime um erro do tipo notice : json (error:true, type:notice, msg:$msg)
	 * @param string $msg
	 */
	public static function noticeDisplay($msg=""){
		HTTPHeader::setReponseJson();
		$arr=array();
		$arr["error"]=true;
		$arr["type"]="notice";
		$arr["msg"]=$msg;
		echo json_encode($arr);
	}
	/**
	 * Imprime o erro para usuario nao logado. Encerra a execucao do programa com um die(json);
	 * @param boolen $redir: se true, irá redirecionar para a pagina de cadastro
	 */
	public static function naoLogado($redir=false){
		HTTPHeader::setReponseJson();
		if($redir){
			include_once PATH_ROOT.'portal/cadastrarNovo2.php';
			exit(0);
		}
		$arr=array();
		$arr["error"]=true;
		$arr["login"]=true;
		$arr["type"]="notice";
		$arr["msg"]="Por favor, realize o login para executar essa ação.";
		die(json_encode($arr));
	}
	/**
	 * Imprime o erro para usuario nao autorizado. Encerra a execucao do programa com um die(json);
	 */
	public static function naoAutorizado(){
		HTTPHeader::setReponseJson();
		HTTPHeader::set(HTTPResponseCode::UNAUTHORIZED);
		$arr=array();
		$arr["error"]=true;
		$arr["type"]="warning";
		$arr["msg"]="Você não está autorizado a realizar esta ação.";
		die(json_encode($arr));
	}
	/**
	 * Imprime o erro para usuario nao autorizado. Encerra a execucao do programa com um die(json);
	 * @var boolean $die: se false retorna um ArrayObj
	 * @return ArrayObj|die
	 */
	public static function paginaNaoEncontrada($die=true){
		HTTPHeader::setReponseJson();
		HTTPHeader::set(HTTPResponseCode::NOT_FOUND);
		$arr=array();
		$arr["msg"]="Página não encontrada :( ";
		if($die)
			die(json_encode($arr));
		else
			return new ArrayObj($arr);
	}
}
?>