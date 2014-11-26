/**
 * This panel holds all verses and requests the right ones from the server.
 * Furthermore the verses can be rearranged in that panel. Philipp Jenni
 */
Ext.namespace('Songserver.view');

/**
 * Config parameters: songPanel {Songserver.view.Song} Reference to the song
 * panel.
 */
Ext.define('Songserver.view.Songcontent', {
    extend : 'Ext.panel.Panel',
    requires : [ 'Songserver.view.VerseViewPanel', 'Songserver.view.RefrainViewPanel' ],
    alias : 'widget.songserver-songcontentPanel',

    // holds the reference to the song panel
    songPanel : null,

    initComponent : function() {
	Ext.apply(this, {
	    title : 'Lied-Inhalte',
	    preventHeader : true,
	    bodyStyle : 'background-color: #DFE8F6; border: none;',
	    defaults : {
		bodyStyle : 'background-color: #DFE8F6; border: none;'
	    },
	    items : [ this.createTitlePanel("Refrains:"), {
		itemId : "refrains"
	    }, this.createTitlePanel("Strophen:"), {
		itemId : "verses"
	    } ]
	});

	Songserver.view.Songcontent.superclass.initComponent.apply(this, arguments);
    },

    createVersePanels : function(verseStore) {
	var versePanels = new Array();
	verseStore.each(function(aVerse, index, allRecords) {
	    var panel = Ext.create("Songserver.view.VerseViewPanel", {
		songtext : aVerse,
		songPanel : this.songPanel
	    });
	    versePanels.push(panel);
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

	// enable the title panel
	Ext.Array.each(this.query('panel[isTitlePanel=true]'), function(aPanel, index, allTitlePanels) {
	    aPanel.show()
	});
	this.doLayout();
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

    /**
     * Updates the refrain references inside the verse panels.
     * 
     * @param {Songserver.model.Refrain}
     *                refrain
     */
    onRefrainChanged : function(refrain) {
	var panels = this.down('#verses').items.getRange();
	Ext.Array.each(panels, function(aPanel, index, allPanels) {
	    aPanel.updateRefrain(refrain);
	});
    },

    /**
     * Creates a new verse form and adds the emtpy panel to the GUI.
     */
    createVerse : function() {
	var p = Ext.create("Songserver.view.VerseViewPanel", {
	    domainObjectId : null, // we want a new object
	    songPanel : this.songPanel
	});

	this.child("#verses").add(p);

	// scroll down
	this.songPanel.body.dom.scrollTop = this.songPanel.body.dom.scrollHeight;
    },

    /*
     * Creates a new CORRECT configured (events) refrain panel and returns it.
     * 
     * @param {Songserver.model.Refrain} refrain
     */
    createRefrainPanel : function(refrain) {
	return Ext.create("Songserver.view.RefrainViewPanel", {
	    songtext : refrain,
	    songPanel : this.songPanel,
	    listeners : {
		refrainChanged : this.onRefrainChanged,
		scope : this
	    }
	});
    }
});