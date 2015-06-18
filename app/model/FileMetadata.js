Ext.namespace('Songserver.model');

Ext.define('Songserver.model.FileMetadata', {
    extend : 'Songserver.model.Base',

    fields : [ {
	name : 'filetype',
	type : 'string'
    }, {
	name : 'lied_id',
	type : 'int'
    } ]

});