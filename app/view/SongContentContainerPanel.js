/**
 * This panel holds all verses and requests the right ones from the server.
 * Furthermore the verses can be rearranged in that panel. Philipp Jenni
 */
Ext.namespace('Songserver.view');

/**
 * Config parameters: songPanel {Songserver.view.Song} Reference to the song
 * panel.
 */
Ext.define('Songserver.view.SongContentContainerPanel', {
    extend : 'Ext.panel.Panel',
    requires : [ 'Songserver.view.VerseViewPanel', 'Songserver.view.RefrainViewPanel' ],
    alias : 'widget.songserver-songContentContainerPanel',

    // holds the reference to the song panel
    songPanel : null,

    title : 'Lied-Inhalte',
    preventHeader : true,
    items : [ {
	xtype : "panel",
	bodyStyle : {
	    backgroundColor : '#f5f5f5'
	},
	margin : '10 0 0 0',
	title : "Strophen",
	collapsible : "true",
	itemId : "verses",
	dockedItems : {
	    xtype : 'toolbar',
	    dock : 'bottom',
	    style : {
		backgroundColor : '#f5f5f5'
	    },
	    items : [ {
		itemId : 'addVerse',
		xtype : 'button',
		icon : 'resources/images/silk/icons/add.png',
		text : 'Strophe hinzufügen',
		listeners : {
		    click : function(button, e) {
			button.up("songserver-songContentContainerPanel").createVersePanel();
		    }
		}
	    } ]
	}
    }, {
	xtype : "panel",
	bodyStyle : {
	    backgroundColor : '#f5f5f5'
	},
	margin : '10 0 0 0',
	title : "Refrains",
	collapsible : "true",
	itemId : "refrains",
	dockedItems : {
	    xtype : 'toolbar',
	    dock : 'bottom',
	    style : {
		backgroundColor : '#f5f5f5'
	    },
	    items : [ {
		itemId : 'addRefrain',
		xtype : 'button',
		toggle : true,
		icon : 'resources/images/silk/icons/add.png',
		text : 'Refrain hinzufügen',
		listeners : {
		    scope : this,
		    click : function(button, e) {
			button.up("songserver-songContentContainerPanel").createRefrainPanel();
		    }
		}
	    } ]
	}
    } ],

    createVersePanels : function(verseStore) {
	var versePanels = new Array();
	verseStore.each(function(aVerse, index, allRecords) {
	    this.createVersePanel(aVerse);
	}, this);
	this.child("#verses").add(versePanels);
    },

    createRefrainPanels : function() {
	var refrainPanels = new Array();
	var store = this.songPanel.refrainStore;
	store.each(function(record, index, allRecords) {
	    refrainPanels.push(this.createRefrainPanel(record));
	}, this);
	this.child("#refrains").add(refrainPanels);
    },

    /**
     * Creates a panel that can be used to group the refrains and verses
     * together by creating a panel with the given title.
     * 
     * @param {String}
     *                title
     */
    createTitlePanel : function(title) {
	return Ext.create("Ext.panel.Panel", {
	    isTitlePanel : true,
	    preventHeader : true,
	    border : false,
	    hidden : true,
	    bodyStyle : {
		padding : '5px',
		background : '#DFE8F6'
	    },
	    html : title
	})
    },

    /**
     * Gets all verse and refrain panels in an Array
     * 
     * @return {Songserver.view.SongtextViewPanel[]} All panels in an array.
     */
    getVerseAndRefrainPanels : function() {
	return Ext.Array.merge(this.down('#verses').items.getRange(), this.down('#refrains').items.getRange());
    },

    onRefrainChanged : function(refrain) {
	var store = this.songPanel.refrainStore;
	var refrainInStore = store.getById(refrain.get("id"));
	if (refrainInStore == null) {
	    store.add(refrain);
	} else {
	    var panels = this.down('#verses').items.getRange();
	    Ext.Array.each(panels, function(aPanel, index, allPanels) {
		aPanel.updateRefrain(refrain);
	    });
	}
    },

    /**
     * Creates a new verse form and adds the emtpy panel to the GUI.
     */
    createVersePanel : function(verse) {
	var p = Ext.create("Songserver.view.VerseViewPanel", {
	    songtext : verse,
	    songPanel : this.songPanel
	});

	this.child("#verses").add(p);

	return p;
    },

    /*
     * Creates a new CORRECT configured (events) refrain panel and adds it to
     * the GUI.
     * 
     * @param {Songserver.model.Refrain} refrain
     */
    createRefrainPanel : function(refrain) {
	var p = Ext.create("Songserver.view.RefrainViewPanel", {
	    songtext : refrain,
	    songPanel : this.songPanel,
	    listeners : {
		refrainChanged : this.onRefrainChanged,
		scope : this
	    }
	});

	this.child("#refrains").add(p);

	return p;
    }
});