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
