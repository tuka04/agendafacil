<?php
/**
 * Manage RESTApi
 */
include_once '/var/www/html/agendafacil/config/defines.php';
include_once PATH_ROOT.'config/includes.php';//classes de utils

//caso seja requisicao para webservice
if(isset($_REQUEST['ws'])){
	require_once PATH_ROOT.'ws/RestAPI.class.php';
	$api=new RestAPI();
	return;
}

?>