Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Refrain', {
    extend : 'Songserver.model.Base',

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
    } ]
});