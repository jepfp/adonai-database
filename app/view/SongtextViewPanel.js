/**
 * This panel displays one songcontent item of a song in read only mode.
 * Additionally it has some buttons to rearrange the order of the items and to
 * edit them. Philipp Jenni
 */
Ext.namespace('Songserver.view');

/**
 * ABSTRACT CLASS Config parameters:
 * 
 * 
 * songPanel - {Songserver.view.Song} Reference to the song panel.
 */

Ext.define('Songserver.view.SongtextViewPanel', {
    extend : 'Ext.panel.Panel',
    requires : [ 'Songserver.model.Refrain', 'Songserver.view.VerseFormPanel' ],
    alias : 'widget.songserver-songtextViewPanel',

    /**
     * The loaded songtext.
     * 
     * @type Songserver.model.Liedtext / Songserver.model.Refrain
     */
    songtext : null,
    // holds a reference to the form panel if one exists
    formPanel : null,
    // holds the reference to the song panel
    songPanel : null,

    tableName : "PLEASE_SPECIFY_IN_SUBCLASS",

    initComponent : function() {
	// console.log("Song öffnen. Id: " + this.songId);
	Ext.apply(this, {
	    title : 'Strophe',
	    preventHeader : true,
	    bodyStyle : {
		padding : '10px'
	    },
	    style : {
		margin : '5px'
	    },
	    minHeight : 158,
	    dockedItems : [ {
		xtype : 'toolbar',
		dock : 'left',
		style : {
		    backgroundColor : '#f5f5f5'
		},
		itemId : "tbar",
		items : this.getToolbarConfiguration()
	    } ]
	});

	this.callParent(arguments);

	// check if a new songtext should be created.
	if (this.songtext == null) {
	    this.prepareForNewSongtext();
	} else {
	    this.loadData();
	}
    },

    /**
     * Returns the toolbar configuration array.
     * 
     * @return {Array} Configuration Array.
     */
    getToolbarConfiguration : function() {
	return [ {
	    name : 'edit',
	    xtype : 'button',
	    icon : 'resources/images/silk/icons/pencil.png',
	    tooltip : 'Bearbeiten',
	    listeners : {
		click : this.edit,
		scope : this
	    }
	}, {
	    name : 'moveUp',
	    xtype : 'button',
	    icon : 'resources/images/silk/icons/arrow_up.png',
	    tooltip : 'Nach oben verschieben',
	    listeners : {
		click : this.moveUp,
		scope : this
	    }
	}, {
	    name : 'moveDown',
	    xtype : 'button',
	    icon : 'resources/images/silk/icons/arrow_down.png',
	    tooltip : 'Nach unten verschieben',
	    listeners : {
		click : this.moveDown,
		scope : this
	    }
	}, {
	    name : 'delete',
	    xtype : 'button',
	    icon : 'resources/images/silk/icons/cross.png',
	    tooltip : 'Löschen',
	    listeners : {
		click : this.deleteIt,
		scope : this
	    }
	}, {
	    name : 'cancel',
	    xtype : 'button',
	    hidden : true,
	    icon : 'resources/images/silk/icons/arrow_left.png',
	    tooltip : 'Änderungen verwerfen',
	    listeners : {
		click : this.cancelEdit,
		scope : this
	    }
	}, {
	    name : 'save',
	    xtype : 'button',
	    hidden : true,
	    icon : 'resources/images/silk/icons/accept.png',
	    tooltip : 'Speichern',
	    listeners : {
		click : this.save,
		scope : this
	    }
	} ];
    },

    edit : function() {
	if (!this.songPanel.requestEditLock(this))
	    return;

	var tbar = this.child("#tbar");
	tbar.child('button[name="moveUp"]').hide();
	tbar.child('button[name="moveDown"]').hide();
	tbar.child('button[name="edit"]').hide();
	tbar.child('button[name="delete"]').hide();
	tbar.child('button[name="cancel"]').show();
	tbar.child('button[name="save"]').show();
	tbar.child('button[name="save"]').enable();

	this.removeAll();

	this.formPanel = this.getNewFormPanel();

	this.add(this.formPanel);
    },

    cancelEdit : function() {
	this.songtext.reject();
	if (this.songtext.get("id") > 0) {
	    this.switchToShowMode();
	    this.loadData();
	} else {
	    this.songPanel.freeEditLock();
	    this.removeThisPanel();
	}
    },

    /**
     * Moves the current text element upwards in the order of appeareance and
     * also persists the change.
     */
    moveUp : function() {
	var index = Ext.Array.indexOf(this.ownerCt.items.items, this);

	// don't do anything, if we are already at the top
	if (index < 1) {
	    return;
	}

	// swap the items
	var ownerCt = this.ownerCt;
	ownerCt.remove(this, false);
	ownerCt.insert(index - 1, this);

	ChangeOrder.moveUp(this.tableName, this.songtext.get("id"), function(result, e) {
	    this.displayOrderResultMessage(result);
	}, this);
    },

    /**
     * Moves the current text element downwards in the order of appeareance and
     * also persists the change.
     */
    moveDown : function() {
	var panels = this.ownerCt.items.items;
	var index = Ext.Array.indexOf(panels, this);

	// don't do anything, if we are already at the top
	if (index == panels.length - 1) {
	    return;
	}

	// swap the items
	var ownerCt = this.ownerCt;
	ownerCt.remove(this, false);
	ownerCt.insert(index + 1, this);

	ChangeOrder.moveDown(this.tableName, this.songtext.get("id"), function(result, e) {
	    this.displayOrderResultMessage(result);
	}, this);
    },

    displayOrderResultMessage : function(isSuccess) {
	if (isSuccess == true) {
	    this.songPanel.displayInfoMessage("Die neue Reihenfolge wurde auf dem Server gespeichert.");
	} else {
	    this.songPanel.displayErrorMessage("Fehler beim Speichern der Reihenfolge.");
	}
    },

    /**
     * Switches the panel from edit mode to show mode.
     */
    switchToShowMode : function() {
	var tbar = this.child("#tbar");
	tbar.child('button[name="moveUp"]').show();
	tbar.child('button[name="moveDown"]').show();
	tbar.child('button[name="edit"]').show();
	tbar.child('button[name="delete"]').show();
	tbar.child('button[name="cancel"]').hide();
	tbar.child('button[name="save"]').hide();

	this.songPanel.freeEditLock();
    },

    save : function() {
	this.formPanel.saveChangesIfNecessary();
    },

    deleteIt : function(promptFirst) {
	if (promptFirst === undefined) {
	    promptFirst = true
	}

	if (promptFirst) {
	    var mbox = Ext.Msg.show({
		title : 'Löschen bestätigen',
		msg : 'Möchtest du diesen Liedteil wirklich löschen?',
		buttons : Ext.Msg.YESNO,
		icon : Ext.Msg.QUESTION,
		fn : function(btn, text) {
		    if (btn == 'yes') {
			this.deleteIt(false);
		    }
		},
		scope : this
	    });
	} else {
	    this.songtext.erase({
		scope : this,
		failure : this.onDeleteFailure,
		success : function(record, operation) {
		    this.removeThisPanel();
		    this.songPanel.displayInfoMessage("Liedteil gelöscht.");
		}
	    });
	}
    },

    /**
     * Removes this panel from the parent songcontent panel.
     */
    removeThisPanel : function() {
	var parentPanel = this.ownerCt;
	var songPanel = this.up("songserver-songPanel");
	parentPanel.remove(this);
	songPanel.doLayout();
    },

    onDeleteFailure : function(record, operation) {
	this.up("songserver-songPanel").displayErrorMessage("Fehler beim Löschen. Bitte informiere den Website-Verantwortlichen über diesen Fehler.");
    },

    /**
     * To be called after the songtext of this panel has been updated.
     * 
     * @param {Songserver.model.Liedtext /
     *                Songserver.model.Refrain} songtext
     */
    onUpdatedSongtext : function(songtext) {
	this.switchToShowMode();
	this.songtext = songtext;
	this.loadData();
    },

    disableEditing : function() {
	var tbar = this.child("#tbar");
	tbar.child('button[name="edit"]').setDisabled(true);
	tbar.child('button[name="moveDown"]').setDisabled(true);
	tbar.child('button[name="moveUp"]').setDisabled(true);
    },

    enableEditing : function() {
	var tbar = this.child("#tbar");
	tbar.child('button[name="edit"]').setDisabled(false);
	tbar.child('button[name="moveDown"]').setDisabled(false);
	tbar.child('button[name="moveUp"]').setDisabled(false);
    },

    /**
     * Prepares this panel so that a new songtext can be added (switching the
     * panel to edit mode and so on).
     */
    prepareForNewSongtext : function() {
	this.songtext = this.getNewModel();
	this.songtext.set("lied_id", this.songPanel.getSong().get("id"));
	this.edit();
    }
});