Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Refrain', {
    extend : 'Ext.data.Model',

    fields : [ {
	name : 'Refrain',
	type : 'string'
    }, {
	name : 'language_id',
	type : 'int'
    }, {
	name : 'lied_id',
	type : 'int'
    } ],

    hasMany : [ {
	// autoLoad is set to false (as default)
	model : 'Songserver.model.Liedtext',
	name : 'liedtexts',
	storeConfig : {
	    remoteFilter : true
	}
    } ],

    proxy : {
	url : 'src/ext-rest-interface.php/refrain',
	type : "rest",
	reader : {
	    type : 'json',
	    root : 'data'
	}
    }

});