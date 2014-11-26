Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Rubrik', {
	extend : 'Ext.data.Model',

	fields : [ {
		name : 'id',
		type : 'int'
	}, {
		name : 'Rubrik',
		type : 'string'
	} ],

	hasMany : [ {
		model : 'Songserver.model.Lied',
		name : 'lieds'
	} ],

	proxy : {
		url : 'src/ext-rest-interface.php/rubrik',
		type : "rest",
		reader : {
			type : 'json',
			root : 'data'
		}
	}

});