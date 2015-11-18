<?php
/**
 * Manage RESTApi
 */

include_once '/var/www/html/agendafacil/config/defines.php';
include_once PATH_ROOT.'config/includes.php';//classes de utils

session_start();

//caso seja requisicao para webservice
if(isset($_REQUEST['ws']) && $_SERVER["REQUEST_URI"]!="/agendafacil/view/teste.php"){
	require_once PATH_ROOT.'ws/RestAPI.class.php';
	$api=new RestAPI();
	return;
}
else{
	include_once PATH_ROOT.'view/teste.php';
}

?>