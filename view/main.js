$(document).ready(function() {
	
	var a=new WS();
	a.url="/agendafacil/usuario/";
	a.success=function(r){
		for(var i in r){
			for(j in r[i])
				$("body").append("<p>"+j+":"+r[i][j]+"</p>");
		}
	};
	a.send();
	
	
	
});