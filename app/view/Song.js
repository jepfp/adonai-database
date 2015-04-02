/**
 * View and edit one single song. Philipp Jenni
 */
Ext.namespace('Songserver.view');

/**
 * Config parameters:
 * 
 * songId - {int} - Id of the song to load or 0 to create a new.
 */

Ext.define('Songserver.view.Song', {
    extend : 'Ext.panel.Panel',
    requires : [ 'Songserver.view.SongPropertiesPanel', 'Songserver.view.Songcontent', 'Ext.container.ButtonGroup' ],
    alias : 'widget.songserver-songPanel',

    hideInfoMessageTask : null,
    /**
     * The panel which is currently in edit mode. If !null the other panels can
     * not switch into edit mode. Null if no panel holds a lock.
     * 
     * @type Songserver.view.SongtextViewPanel
     */
    holdingLockPanel : null,

    verseStore : null,

    refrainStore : null,

    bodyStyle : {
	overflowX: "hidden",
	overflowY: "scroll"
    },
    
//    scrollable: true,
    
    layout: {
        type: 'anchor',
        reserveScrollbar: true // There will be a gap even when there's no scrollbar
    },
    scrollable: false,
    
    initComponent : function() {

	// console.log("Song öffnen. Id: " + this.songId);
	Ext.apply(this, {
	    title : 'Lied bearbeiten',
	    items : [ {
		xtype : 'songserver-songPropertiesPanel',
		songId : this.songId,
		// config for
		// Ext.form.Base so that
		// the form
		// can be resetted to
		// the load values and
		// not to
		// empty values
		trackResetOnLoad : true,
		listeners : {
		    scope : this,
		    songLoaded : this.onSongLoaded
		}
	    }, {
		xtype : 'songserver-songcontentPanel',
		songPanel : this
	    } ],
	    tbar : [ {
		itemId : 'cancel',
		xtype : 'button',
		icon : 'resources/images/silk/icons/arrow_turn_left.png',
		text : 'Zurück zur Übersicht',
		listeners : {
		    scope : this,
		    click : function(button, e) {
			Songserver.AppContext.mainLayout.loadPanel("Songserver.view.LiedView");
		    }
		}
	    }, {
		itemId : 'addVerse',
		xtype : 'button',
		icon : 'resources/images/silk/icons/add.png',
		text : 'Strophe hinzufügen',
		listeners : {
		    scope : this,
		    click : function(button, e) {
			this.child("songserver-songcontentPanel").createVerse();
		    }
		}

	    }, {
		itemId : 'addRefrain',
		xtype : 'button',
		toggle : true,
		icon : 'resources/images/silk/icons/add.png',
		text : 'Refrain hinzufügen',
		listeners : {
		    scope : this,
		    click : function(button, e) {
			var songcontentPanel = this.child("songserver-songcontentPanel");
			var rp = songcontentPanel.createRefrainPanel();
			songcontentPanel.child("#refrains").add(rp);
		    }
		}
	    } ],
	    bbar : {
		itemId : "messageBar",
		height : '25'
	    }
	});

	this.callParent();

	this.hideInfoMessageTask = new Ext.util.DelayedTask(function() {
	    if (this != null && this.child("#messageBar") != null) {
		this.child("#messageBar").removeAll();
	    }
	}, this);

	// if the user wants to add a new song...
	if (!this.songId) {
	    this.prepareFormForAddingNewItem();
	}
    },

    /**
     * Event handler to be called, when a song is loaded. parameters: song -
     * Songserver.model.Song
     */
    onSongLoaded : function(song) {
	this.verseStore = song.liedtexts();
	this.verseStore.load({
	    scope : this,
	    callback : this.songBelongingsCollector
	});
	this.refrainStore = song.refrains();
	this.refrainStore.load({
	    scope : this,
	    callback : this.songBelongingsCollector
	});
    },

    getRefrainByVerse : function(aVerse) {
	var refrain_id = aVerse.get("refrain_id");
	var refrain = this.refrainStore.findRecord("id", refrain_id);
	return refrain;
    },

    songBelongingsCollector : function(records, operation, success) {
	if (this.verseStore.loading || this.refrainStore.loading) {
	    // wait for the second one
	    return;
	}

	this.down("songserver-songcontentPanel").createVersePanels(this.verseStore);
	this.down("songserver-songcontentPanel").createRefrainPanels();
    },

    /**
     * Displays an info message in the footer toolbar. This message will
     * automatically be hidden after a couple of seconds.
     * 
     * @param {string}
     *                text The text to display
     * @param {int}
     *                seconds The amount of seconds the text shall be displayed
     *                (default is set to 6000 ms).
     */
    displayInfoMessage : function(text, seconds) {
	if (!seconds)
	    seconds = 6000;
	this.child("#messageBar").removeAll();
	this.child("#messageBar").add(text);
	this.hideInfoMessageTask.delay(seconds);
    },

    /**
     * Displays an error message in the footer toolbar. This message will
     * automatically be hidden after a couple of seconds.
     * 
     * @param {string}
     *                text The text to display
     * @param {int}
     *                seconds The amount of seconds the text shall be displayed
     *                (default is set to 15000 ms).
     */
    displayErrorMessage : function(text, seconds) {
	if (!seconds)
	    seconds = 15000;
	this.child("#messageBar").removeAll();
	this.child("#messageBar").add("<div class='footerErrorMessage'>" + text + "</div>");
	this.hideInfoMessageTask.delay(seconds);
    },

    /**
     * Returns the current opened / editing Songserver.model.Song.
     */
    getSong : function() {
	return this.child("songserver-songPropertiesPanel").song;
    },

    requestEditLock : function(requestingPanel) {
	if (this.holdingLockPanel != null)
	    return false;

	this.holdingLockPanel = requestingPanel;

	var panels = this.child("songserver-songcontentPanel").getVerseAndRefrainPanels();

	Ext.Array.each(panels, function(aPanel, index, allPanels) {
	    aPanel.disableEditing();
	});

	this.down("#addRefrain").setDisabled(true);
	this.down("#addVerse").setDisabled(true);
	this.down("#cancel").setDisabled(true);

	return true;
    },

    freeEditLock : function() {

	var panels = this.child("songserver-songcontentPanel").getVerseAndRefrainPanels();

	Ext.Array.each(panels, function(aPanel, index, allPanels) {
	    aPanel.enableEditing();
	});

	this.down("#addRefrain").setDisabled(false);
	this.down("#addVerse").setDisabled(false);
	this.down("#cancel").setDisabled(false);

	this.holdingLockPanel = null;

	return true;
    },

    /**
     * This funciton prepares the form for the user so that he can add a new
     * item. Some not allowed buttons for example are disabled.
     */
    prepareFormForAddingNewItem : function() {
	this.down("#addVerse").setDisabled(true);
	this.down("#addRefrain").setDisabled(true);
    },

    /**
     * Switches to the edit mode after a new song has been created.
     * 
     * @see prepareFormForAddingNewItem()
     * @param {Songserver.model.Song}
     *                song The new created song.
     */
    switchToEditMode : function(song) {
	this.songId = song.get("id");
	this.down("#addVerse").setDisabled(false);
	this.down("#addRefrain").setDisabled(false);
    }
});