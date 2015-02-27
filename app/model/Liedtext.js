Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Liedtext', {
    extend : 'Songserver.model.Base',

    fields : [ {
	name : 'Strophe',
	type : 'string'
    }, {
	name : 'refrain_id',
	type : 'int'
    }, {
	name : 'language_id',
	type : 'int'
    }, {
	name : 'lied_id',
	type : 'int'
    } ]

    // not needed for now as the refrains are loaded over a seperate store
    // associations : [ {
    // type : 'belongsTo',
    // model : 'Songserver.model.Refrain',
    // foreignKey : 'refrain_id',
    // getterName : 'getRefrain'
    // } ],

});