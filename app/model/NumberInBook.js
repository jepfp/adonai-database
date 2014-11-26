Ext.namespace('Songserver.model');

Ext.define('Songserver.model.NumberInBook', {
    extend : 'Ext.data.Model',

    fields : [ {
	name : 'Liednr',
	type : 'string'
    }, {
	name : 'liederbuch_id',
	type : 'int'
    }, {
	name : 'lied_id',
	type : 'int'
    }, {
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
    } ],

    belongsTo : [ 'Lied' ],

    proxy : {
	url : 'src/ext-rest-interface.php/numberInBook',
	type : "rest",
	reader : {
	    type : 'json',
	    root : 'data'
	}
    },

    save : function(options) {
	// This is needed, because we get all records from the server. Even not
	// created ones are returned with id = null as a "tempalte" for the
	// user.
	// Because of that, the comment in ext\src\data\reader\Reader.js for us
	// is NOT true:
	// "If the server did not include an id in the response data, the Model
	// constructor will mark the record as phantom.
	// We need to set phantom to false here because records created from a
	// server response using a reader by definition are not phantom
	// records."
	if (this.get("id") < 1) {
	    this.phantom = true;
	}
	this.callParent(options);
    }

});