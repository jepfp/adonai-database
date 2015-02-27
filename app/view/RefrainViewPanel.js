/**
 * This panel displays one refrain of a song in read only mode. Additionally it
 * has some buttons to rearrange the order of the items and to edit them.
 * Philipp Jenni
 */
Ext.namespace('Songserver.view');

/**
 * Config parameters:
 * 
 * songtext - {Songserver.model.Refrain} Refrain to display.
 * 
 * songPanel - {Songserver.view.Song} Reference to the song panel.
 * 
 * Events: - refrainChanged: Fired, when a refrain has been changed.
 */
Ext.define('Songserver.view.RefrainViewPanel', {
    extend : 'Songserver.view.SongtextViewPanel',
    requires : [ 'Songserver.view.SongtextViewPanel', 'Songserver.model.Refrain', 'Songserver.view.RefrainFormPanel' ],
    alias : 'widget.songserver-refrainViewPanel',

    tableName : "refrain",

    initComponent : function() {
	Ext.apply(this, {
	    title : 'Refrain'
	});

	this.callParent(arguments);
    },

    /**
     * Loads the refrain and displays them / it.
     */
    loadData : function() {
	this.removeAll();
	var htmlContent = this.songtext.get("Refrain");
	var contentPanel = Ext.create('Ext.panel.Panel', {
	    border : 0,
	    html : htmlContent
	});
	this.add(contentPanel);
    },

    /**
     * To be called after the songtext of this panel has been updated.
     * 
     * @param {Songserver.model.Liedtext /
     *                Songserver.model.Refrain} songtext
     */
    onUpdatedSongtext : function(songtext) {
	this.callParent(arguments);
	this.fireEvent("refrainChanged", songtext);
    },

    onDeleteFailure : function(record, operation) {
	this.up("songserver-songPanel").displayErrorMessage("Fehler beim Löschen.");
	Ext.Msg.alert('Fehler', 'Der Refrain konnte nicht gelöscht werden.<br>Prüfe, ob es Strophen gibt, die diesen Refrain verwenden.');
    },

    /**
     * Creates a new panel which allows editing this verse.
     * 
     * @return {Songserver.view.VerseFormPanel} The editing form panel.
     */
    getNewFormPanel : function() {
	return Ext.create('Songserver.view.RefrainFormPanel', {
	    songtext : this.songtext,
	    songPanel : this.songPanel,
	    listeners : {
		updatedSongtext : this.onUpdatedSongtext,
		scope : this
	    }
	});
    },

    getNewModel : function() {
	return Ext.create("Songserver.model.Refrain");
    }
});