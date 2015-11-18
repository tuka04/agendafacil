<?php

final class Validator {

    /**
     * @param string $email
     * @throws ValidatorException
     */
    public static function email($email) {
    	$p="^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+.([a-zA-Z]{2,4})$";//pattern
    	if(!ereg($p,$email))
    		throw new ValidatorException(ValidatorException::EMAIL);
    }
    public static function numeroNatural($n) {
    	if (intval($n) <= 0)
    		throw new ValidatorException(ValidatorException::NUM_NATURAL);
    }
    public static function inteiro($n) {
    	if (!is_int($n)||!Common::isTheSameVarIntString($n))
    		throw new ValidatorException(ValidatorException::NUM_INTEIRO);
    }
    public static function obrigatorio($str) {
    	if (trim($str)=="")
    		throw new ValidatorException(ValidatorException::OBRIGATORIO);
    }    
    public static function data($ano,$mes,$dia){
    	if(!checkdate($mes, $dia, $ano))
    		throw new ValidatorException(ValidatorException::DATA);
    }
    public static function conjuntoNaoVazio($c){
    	if(!is_a($c,ArrayObj::__CLASS__NAME__))
    		throw new ValidatorException(ValidatorException::CONJUNTO_NAO_VAZIO);
    	foreach($c as $v){
    		if(trim($v)=="")
    			throw new ValidatorException(ValidatorException::CONJUNTO_NAO_VAZIO);
    	}
    }
    public static function conjunto($c){
    	if(!is_a($c,ArrayObj::__CLASS__NAME__))
    		throw new ValidatorException(ValidatorException::CONJUNTO);
    }
}

final class ValidatorException extends RuntimeException {

    const EMAIL = "MSG_EMAIL";
    const EMAIL_CADASTRADO="MSG_EMAIL_CADASTRADO";
    const NOME = "MSG_NOME";
    const NOME_CADASTRADO = "MSG_NOME_CADASTRADO";
    const NUM = "MSG_NUM";
    const NUM_INTEIRO = "MSG_NUM_INT";
    const NUM_NATURAL = "MSG_NUM_NATURAL";
    const CONJUNTO_NUM_NATURAL = "MSG_CONJUNTO_NUM_NATURAL";
    const CONJUNTO_NAO_VAZIO = "MSG_CONJUNTO_NAO_VAZIO";
    const CONJUNTO = "MSG_CONJUNTO";
    const OBRIGATORIO = "MSG_OBRIGATORIO";
    const DATA="MSG_DATA";

    private static $MSG_EMAIL = "Email inválido :/ Por favor, verifique o campo de email e digite-o novamente.";
    private static $MSG_EMAIL_CADASTRADO = "O email informado já está cadastrado em nosso sistema. Caso não lembre sua senha, clique em \"recuperar senha\".";
    private static $MSG_NOME = "Nome inválido :/ Por favor, verifique o campo nome e digite-o novamente.";
    private static $MSG_NOME_CADASTRADO = "O nome informado já está cadastrado em nosso sistema.";
    private static $MSG_NUM= "Campo deverá ser um número(int, double,float).";
    private static $MSG_NUM_INT= "Campo deverá ser um inteiro.";
    private static $MSG_NUM_NATURAL= "Campo deverá ser um inteiro maior do que zero.";
    private static $MSG_CONJUNTO_NUM_NATURAL= "Campo deverá ser um conjunto de inteiro maior do que zero.";
    private static $MSG_OBRIGATORIO= "O campo é obrigatório.";
    private static $MSG_DATA= "A data está inválida.";
    private static $MSG_CONJUNTO_NAO_VAZIO="Campo deverá ser um ArrayObj sem nenhum campo vazio";
    private static $MSG_CONJUNTO="Campo deverá ser um ArrayObj.";

    /**
     * @param int $type
     * @throws ErrorException
     */
    public function __construct($type) {
        if (strpos($type, "MSG_") !== false)
            parent::__construct(self::$$type);
        else
            throw new ErrorException("Parâmetro inválido. Não há uma opção para o tipo de exceção.");
    }

}
