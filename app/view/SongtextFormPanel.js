/**
 * View and edit the attributes of one single songtext. This panel goes into the
 * SongtextView panel. Philipp Jenni
 */
Ext.namespace('Songserver.view');

/**
 * ABSTRACT CLASS Config parameters:
 * 
 * songtext - {Songserver.model.Liedtext / Songserver.model.Refrain} - Songtext
 * to edit.
 * 
 * songPanel - {Songserver.view.Song} Reference to the song panel.
 * 
 * Events: - updatedSongtext: Will be fired when the changes have been saved on
 * the server. loaded.
 */

Ext.define('Songserver.view.SongtextFormPanel', {
    extend : 'Ext.form.Panel',
    requires : [ 'Songserver.view.RefrainCardPanel', 'Ext.form.FieldContainer' ],
    alias : 'widget.songserver-songtextFormPanel',

    // The songtext object that we want to edit
    songtext : null,
    // holds the reference to the song panel
    songPanel : null,
    /**
     * The name of the content field which will be the attribute key for the
     * form that is sent to the server (e. g. verse / refrain).
     * 
     * @type String
     */
    songtextFieldName : "PLEASE_SPECIFY_IN_SUBCLASS",

    bodyStyle : 'border: none;',
    preventHeader : true,

    items : [ {
	xtype : 'fieldcontainer',
	fieldDefaults : {
	    msgTarget : 'side',
	    labelWidth : 75
	},
	defaultType : 'textfield',
	defaults : {
	    width : 300
	},
	items : [ {
	    xtype : 'textareafield',
	    name : 'TOBECHANGED',
	    itemId : 'songtextFieldName',
	    grow : true,
	    hideLabel : true,
	    anchor : '100%'
	} ]
    } ],

    initComponent : function() {
	this.callParent();

	this.down("#songtextFieldName").name = this.songtextFieldName;

	this.loadRecord(this.songtext);

	this.replaceBrTags();
    },

    saveChangesIfNecessary : function() {
	this.songPanel.setLoading(true);
	var form = this.getForm();
	this.formatFields();
	this.songtext.set(form.getValues());

	this.up("songserver-songPanel").displayInfoMessage("Text wird gespeichert. Bitte warten...");

	if (this.songtext.dirty) {
	    this.saveChanges();
	} else {
	    this.cancelEdit();
	}
    },

    saveChanges : function() {
	this.songtext.save({
	    success : function(record, operation) {
		this.up("songserver-songPanel").displayInfoMessage("Die Änderungen wurden gespeichert.");

		// Update the current this.songtext with the new data
		// This is important espacially when we have added a
		// songtext.
		this.songtext = record;

		this.fireEvent("updatedSongtext", record);
		this.songPanel.setLoading(false);
	    },
	    failure : function(record, operation) {
		this.up("songserver-songPanel").displayErrorMessage(
			"Fehler beim Speichern! Bitte versuche es nochmals oder melde den Fehler dem Website-Verantwortlichen.");
		this.songPanel.setLoading(false);
	    },
	    scope : this
	});
    },

    cancelEdit : function() {
	this.songPanel.setLoading(false);
	this.up("songserver-songtextViewPanel").cancelEdit();
    },

    /**
     * Removes all HTML-Tags and replaces the \n characters by <br>
     * tags.
     */
    formatFields : function() {
	var f = this.getForm().findField(this.songtextFieldName);
	var s = new String(f.getValue());
	s = Ext.util.Format.stripTags(s);
	s = Ext.util.Format.nl2br(s);
	f.setValue(s);
    },

    /**
     * Replaces all the <br>
     * tags in the form by '\n' characters.
     */
    replaceBrTags : function() {
	var f = this.getForm().findField(this.songtextFieldName);
	var s = new String(f.getValue());
	s = s.replace(/<br[ ]*[\/]*>/gi, "\n");
	f.setValue(s);
    }
});