/**
 * Represents a single song with his properties as it is shown on the GUI (does
 * not include the text of the song). Philipp Jenni
 */
Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Lied', {
    extend : 'Ext.data.Model',

    fields : [ /*{
	name : 'id',
	type : 'int'
    },*/ {
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
    } ],

    hasMany : [ {
	model : 'Songserver.model.NumberInBook',
	name : 'bookentries',
	storeConfig : {
	    autoLoad : true,
	    remoteFilter : true
	}
    }, {
	//autoLoad is set to false (as default)
	model : 'Songserver.model.Liedtext',
	name : 'liedtexts',
	storeConfig : {
	    remoteFilter : true
	}
    }, {
	//autoLoad is set to false (as default)
	model : 'Songserver.model.Refrain',
	name : 'refrains',
	storeConfig : {
	    remoteFilter : true
	}
    } ],

    proxy : {
	url : 'src/ext-rest-interface.php/lied',
	type : "rest",
	reader : {
	    type : 'json',
	    root : 'data'
	}
    }

});