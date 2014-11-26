/**
 * Represents a single songbook with his properties as it is shown on the GUI.
 * Philipp Jenni
 */
Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Songbook', {
	extend : 'Ext.data.Model',

	fields : [ {
		name : 'id',
		type : 'int'
	}, {
		name : 'number',
		type : 'string'
	}, {
		name : 'bookname',
		type : 'string'
	}, {
		name : 'description',
		type : 'string'
	} ],
	
	belongsTo: 'Songserver.model.Song'

});