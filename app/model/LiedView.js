/**
 * Represents a single song for the overview view.
 */
Ext.namespace('Songserver.model');

Ext.define('Songserver.model.LiedView', {
	extend : 'Ext.data.Model',

	fields : [ {
		name : 'Liednr',
		type : 'string'
	}, {
		name : 'Titel',
		type : 'string'
	}, {
		name : 'Rubrik',
		type : 'string'
	}, {
		name : 'tonality',
		type : 'string'
	}, {
		name : 'created_at',
		type : 'string'
	}, {
		name : 'updated_at',
		type : 'string'
	}, {
		name : 'email',
		type : 'string'
	} ],

	proxy : {
		url : 'src/ext-rest-interface.php/liedView',
		type : "rest",
		reader : {
			type : 'json',
			rootProperty : 'data',
			totalProperty : 'totalCount'
		}

	}

});