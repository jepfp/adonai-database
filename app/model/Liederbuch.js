Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Liederbuch', {
    extend : 'Songserver.model.Base',

    fields : [ {
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
    } ]
});