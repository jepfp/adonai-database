/**
 * Represents a single song with his properties as it is shown on the GUI (does
 * not include the text of the song). Philipp Jenni
 */
Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Lied', {
    extend : 'Songserver.model.Base',

    fields : [ {
	name : 'Titel',
	type : 'string'
    }, {
	name : 'rubrik_id',
	type : 'int'
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
	name : 'lastEditUser_id',
	type : 'int'
    }],

    hasMany : [ {
	// autoLoad is set to false (as default)
	model : 'Songserver.model.FileMetadata',
	name : 'files',
	storeConfig : {
	    remoteFilter : true
	}
    }, {
	// autoLoad is set to false (as default)
	model : 'Songserver.model.Liedtext',
	name : 'liedtexts',
	storeConfig : {
	    remoteFilter : true
	}
    }, {
	// autoLoad is set to false (as default)
	model : 'Songserver.model.Refrain',
	name : 'refrains',
	storeConfig : {
	    remoteFilter : true
	}
    } ],

    hasOne : [ {
	name : 'user',
	getterName : 'getUser',
	setterName : 'setUser',
	model : 'Songserver.model.User',
	primaryKey : 'id',
	foreignKey : 'lastEditUser_id'
    } ]
});