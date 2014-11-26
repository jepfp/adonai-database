Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Liederbuch', {
    extend : 'Ext.data.Model',

    fields : [ {
	name : 'id',
	type : 'int'
    }, {
	name : 'Buchname',
	type : 'string'
    }, {
	name : 'Beschreibung',
	type : 'string'
    }, {
	name : 'mnemonic',
	type : 'string'
    }, {
	name : 'locked',
	type : 'boolean'
    } ],

    proxy : {
	url : 'src/ext-rest-interface.php/liederbuch',
	type : "rest",
	reader : {
	    type : 'json',
	    root : 'data'
	}
    }

});