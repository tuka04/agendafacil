var WS=function(){
	this.url = "";
	this.async = true;
	this.param = {};
	this.dataType = "json";
	this.success = function(r) {};
	this.beforeSend = function() {};
	this.complete = function(r, s) {};
	this.error = function(a, b, c) {};
	this.type="GET";
	this.send = function() {
		var t = this;
		return $.ajax({
			url : t.url,
			dataType : t.dataType,
			type : t.type,
			data : JSON.stringify(t.param),
			async : t.async,
			beforeSend : function() {
				t.beforeSend();
			},
			complete : function(r, s) {
				t.complete(r, s);
			},
			error : function(r, s, text) {
				t.error(r, s, text);
			},
			success : function(r) {
				t.success(r);
			}
		});
	};
};

var GetFormData=function($e){
	return JSON.parse('{"' + decodeURI($e.serialize()).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
};

var Login={
		autenticar:function(e,s){
			var a=new WS();
			a.url="/agendafacil/login/";
			a.type="GET";
			a.param={email:e,senha:s};
			a.success=function(r){
				window.location.href=window.location.href;
			};
			a.send();
		},
		sair:function(){
			var a=new WS();
			a.url="/agendafacil/login/";
			a.type="DELETE";
			a.param={email:e,senha:s};
			a.success=function(r){
				window.location.href=window.location.href;
			};
			a.send();
		}
};