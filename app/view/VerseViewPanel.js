/**
 * This panel displays one verse of a song in read only mode. Additionally it
 * has some buttons to rearrange the order of the verses and to edit them.
 * Philipp Jenni
 */
Ext.namespace('Songserver.view');

Ext.define('Songserver.view.VerseViewPanel', {
    extend : 'Songserver.view.SongtextViewPanel',
    requires : [ 'Songserver.view.SongtextViewPanel', 'Songserver.model.Refrain', 'Songserver.view.VerseFormPanel' ],
    alias : 'widget.songserver-verseViewPanel',

    tableName : "liedtext",

    initComponent : function() {
	Ext.apply(this, {
	    title : 'Strophe'
	});

	this.callParent();
    },

    loadData : function() {
	this.removeAll();
	var htmlContent = "";
	var refrain = this.songtext.getRefrainInStore();
	if (refrain != null) {
	    htmlContent = "<div class='songRefrain'>" + refrain.get("Refrain") + "</div><br>";
	}
	htmlContent += this.songtext.get("Strophe");
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
	// this.callParent(arguments); don't do this. It's not needed.

	this.switchToShowMode();
	this.domainObjectId = songtext.get("id");
	this.onVerseLoaded(songtext, null);
    },

    /**
     * Creates a new panel which allows editing this verse.
     * 
     * @return {Songserver.view.VerseFormPanel} The editing form panel.
     */
    getNewFormPanel : function() {
	return Ext.create('Songserver.view.VerseFormPanel', {
	    songtext : this.songtext,
	    songPanel : this.songPanel,
	    listeners : {
		updatedSongtext : this.onUpdatedSongtext,
		scope : this
	    }
	});
    },

    /**
     * Takes the given refrain and checks, if the id of the refrain is the same
     * as the currently assigned. If this is the case, the current refrain is
     * replaced with the given one and the text inside the panel gets updated.
     * 
     * @param {Songserver.model.Refrain}
     *                refrain
     */
    updateRefrain : function(refrain) {
	if (refrain.get("id") != this.songtext.getRefrainInStore().get("id")){
	    return;
	}

	this.loadData();
    },

    getNewModel : function() {
	return Ext.create("Songserver.model.Liedtext");
    }
});