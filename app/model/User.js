/**
 * Represents a single song with his properties as it is shown on the GUI (does
 * not include the text of the song). Philipp Jenni
 */
Ext.namespace('Songserver.model');

Ext.define('Songserver.model.User', {
    extend : 'Ext.data.Model',

    fields : [ {
	name : 'email',
	type : 'string'
    }, {
	name : 'firstname',
	type : 'string'
    }, {
	name : 'lastname',
	type : 'string'
    }, {
	name : 'additionalInfos',
	type : 'string'
    }, {
	name : 'active',
	type : 'boolean'
    }],

    proxy : {
	url : 'src/ext-rest-interface.php/user',
	type : "rest",
	reader : {
	    type : 'json',
	    rootProperty : 'data'
	}
    }

});