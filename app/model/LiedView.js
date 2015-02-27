/**
 * Represents a single song for the overview view.
 */
Ext.namespace('Songserver.model');

Ext.define('Songserver.model.LiedView', {
    extend : 'Songserver.model.Base',

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
	} ]
});