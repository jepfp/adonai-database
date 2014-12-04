/**
 * View and edit the attributes of one single verse. This panel goes into the
 * SongtextView panel. Philipp Jenni
 */
Ext.namespace('Songserver.view');

Ext.define('Songserver.view.VerseFormPanel', {
    extend : 'Songserver.view.SongtextFormPanel',
    requires : [ 'Songserver.view.RefrainCardPanel', 'Songserver.view.SongtextFormPanel' ],
    alias : 'widget.songserver-verseFormPanel',

    /**
     * The name of the content field which will be the attribute key for the
     * form that is sent to the server (e. g. verse / refrain).
     * 
     * @type String
     */
    songtextFieldName : "Strophe",

    initComponent : function() {
	this.callParent();

	this.addRefrainCardPanel();
    },

    /**
     * Adds a new RefrainCardPanel and configures it accordingly.
     */
    addRefrainCardPanel : function() {
	var s = this.songPanel.getSong();
	var refrainId = this.songtext.get("refrain_id");
	var panel = Ext.create("Songserver.view.RefrainCardPanel", {
	    width : 300,
	    song : s,
	    selectedRefrainId : refrainId,
	    songPanel : this.songPanel,
	    style : {
		paddingBottom : "5px"
	    },
	    listeners : {
		selectionChanged : this.onRefrainSelectionChanged,
		scope : this
	    }
	});

	this.add(0, panel);
    },

    /**
     * Handler for the case when the user selects another refrain inside the
     * RefrainCardPanel.
     * 
     * @param {int}
     *                selectedRefrain The new selected refrain
     */
    onRefrainSelectionChanged : function(selectedRefrain) {
	this.songtext.setRefrainInStore(selectedRefrain);
    }
});