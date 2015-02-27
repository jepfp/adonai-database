Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Rubrik', {
    extend : 'Songserver.model.Base',

    fields : [ {
	name : 'Rubrik',
	type : 'string'
    } ],

    hasMany : [ {
	model : 'Songserver.model.Lied',
	name : 'lieds'
    } ]

});