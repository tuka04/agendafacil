<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="/agendafacil/view/util/classes/main.js"></script>
<script type="text/javascript" src="/agendafacil/view/main.js"></script>
</head>
<body>


<?php
require_once 'model/Usuario.class.php';
if(Usuario::isLogado()){
?>
	<input type="button" id="logout" value="Sair"/>
<?php 
}
else{
?>
	<form id="login">
	  Email: <input type="text" name="email">
	  Senha: <input type="password" name="senha">
	  <input type="submit">
	</form>
<?php
}
?>
</body>
</html>
