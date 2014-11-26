/**
 * Represents a single song with his properties as it is shown on the GUI (does
 * not include the text of the song). Philipp Jenni
 */
Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Song', {
    extend : 'Ext.data.Model',
    requires : [ 'Songserver.model.Songbook', 'Songserver.SymfonyProxy' ],

    fields : [ {
	name : 'id',
	type : 'int'
    }, {
	name : 'title',
	type : 'string'
    }, {
	name : 'category',
	type : 'string'
    }, {
	name : 'categoryId',
	type : 'string'
    }, {
	name : 'createdAt',
	type : 'string'
    }, {
	name : 'updatedAt',
	type : 'string'
    }, {
	name : 'verses',
	type : "auto"
    } ],

    hasMany : [ {
	model : 'Songserver.model.Songbook',
	name : 'songbooks'
    }, {
	model : 'Songserver.model.Refrain',
	name : 'refrains',
	foreignKey : 'lied_id'
    } ],

    proxy : {
	// DEV:url : 'symfony/web/webservice_dev.php/lied',
	url : 'sfWeb/index.php/lied',
	type : "symfonyProxy",
	// we use our own writer (see SongProxyWriter.js) because we
	// want to automatically add the associations to the songbooks
	// when saving a song.
	writer : "songWriter"
    },

    save : function(options) {
	// reader/Reader.js extractData sets (since extjs 4.1) phantom =
	// false but in case of a new record (which in our case also
	// comes from the server) we need phantom = true
	if (this.get("id") == 0) {
	    this.phantom = true;
	}
	this.callParent(arguments);
    }

});