$(document).ready(function() {
	$("#login").on("submit",function(){
		var d=GetFormData($(this));
		Login.autenticar(d.email,d.senha);
		return false;
	});
	$("#logout").on("click",function(){
		Login.sair();
	});
});

