/**
 * View and edit the attributes of one single song. This panel goes into the
 * Song panel. Philipp Jenni
 */
Ext.namespace('Songserver.view');

/**
 * Events: - songLoaded: Will be fired the first time after the song has been
 * loaded.
 */
Ext.define('Songserver.view.SongPropertiesPanel', {
    extend : 'Ext.form.Panel',
    requires : [ 'Songserver.model.Lied', 'Ext.form.FieldContainer' ],
    alias : 'widget.songserver-songPropertiesPanel',

    // The song object that we want to edit
    song : null,
    // The grid with the associated and available songbooks
    songbookGrid : null,

    title : 'Details zum Lied',
    url : '',
    bodyStyle : 'padding:5px; background-color: #f5f5f5;',
    preventHeader : true,
    layout : {
	type : 'hbox',
	clearInnerCtOnLayout : true
    },

    initComponent : function() {
	// console.log("Song öffnen. Id: " + this.songId);

	Ext.apply(this, {

	    items : [ {
		xtype : 'fieldcontainer',
		width : 450,
		fieldDefaults : {
		    msgTarget : 'side',
		    labelWidth : 75
		},
		defaultType : 'textfield',
		layout : 'anchor',
		defaults : {
		    anchor : '100%'
		},
		items : [ {
		    fieldLabel : 'Titel',
		    name : 'Titel',
		    allowBlank : false,
		    enableKeyEvents : true
		}, {
		    fieldLabel : 'Tonart',
		    name : 'tonality',
		    xtype : 'combobox',
		    emptyText : 'Tonart wählen...',
		    forceSelection : false,
		    editable : false,
		    queryMode : 'local',
		    allowBlank : true,
		    store : [ [ "", "Unbekannt" ],//
		    [ "C / a (kein Vorzeichen)", "C / a (kein Vorzeichen)" ],//
		    [ "G / e (1♯)", "G / e (1♯)" ],//
		    [ "D / h (2♯)", "D / h (2♯)" ],//
		    [ "A / fis (3♯)", "A / fis (3♯)" ],//
		    [ "E / cis (4♯)", "E / cis (4♯)" ],//
		    [ "H / gis (5♯)", "H / gis (5♯)" ],//
		    [ "Fis / dis (6♯)", "Fis / dis (6♯)" ],//
		    [ "Cis / ais (7♯)", "Cis / ais (7♯)" ],//
		    [ "F / d (1♭)", "F / d (1♭)" ],//
		    [ "B / g (2♭)", "B / g (2♭)" ],//
		    [ "Es / c (3♭)", "Es / c (3♭)" ],//
		    [ "As / f (4♭)", "As / f (4♭)" ],//
		    [ "Des / b (5♭)", "Des / b (5♭)" ],//
		    [ "Ges / es (6♭)", "Ges / es (6♭)" ],//
		    [ "Ces / as (7♭)", "Ces / as (7♭)" ] ]
		}, {
		    fieldLabel : 'Rubrik',
		    name : 'rubrik_id',
		    xtype : 'combobox',
		    valueField : 'id',
		    displayField : 'Rubrik',
		    emptyText : 'Kategorie wählen...',
		    forceSelection : true,
		    editable : false,
		    queryMode : 'local',
		    allowBlank : false,
		    store : {
			model : 'Songserver.model.Rubrik',
			autoLoad : true,
			storeId : 'categoryChoice',
			listeners : {
			    load : function(store, records, successful, operation, eOpts) {
				// Load the song after all
				// rubriks have
				// been loaded
				this.loadOrCreateSong();
			    },
			    scope : this
			}
		    }
		}, {
		    fieldLabel : 'Erstellt',
		    name : 'created_at',
		    xtype : 'displayfield'
		}, {
		    fieldLabel : 'Geändert',
		    name : 'updated_at',
		    xtype : 'displayfield'
		}, {
		    fieldLabel : 'Von',
		    itemId : 'user',
		    xtype : 'displayfield'
		} ]
	    } ],

	    dockedItems : [ {
		xtype : 'toolbar',
		itemId : 'footerBar',
		dock : 'bottom',
		ui : 'footer',
		hidden : true,
		autoRender : true,
		style : {
		    margin : '0px',
		    paddingBottom : '5px',
		    backgroundColor : '#f5f5f5;'
		},

		items : [ {
		    text : 'Speichern',
		    formBind : true,
		    // only enabled once the form is valid
		    disabled : true,
		    handler : this.saveChanges,
		    scope : this
		}, {
		    text : 'Änderungen verwerfen',
		    width : 170,
		    handler : this.resetChanges,
		    scope : this
		} ]
	    } ]
	});

	this.callParent(arguments);
    },

    loadOrCreateSong : function() {
	if (this.songId) {
	    Songserver.model.Lied.load(this.songId, {
		scope : this,
		failure : function(record, operation) {
		    Ext.Msg.alert("Fehler beim Laden", "Das Lied konnte nicht geladen werden. " + "Bitte informiere den Website-Verantwortlichen über diesen Fehler.");
		},
		success : function(record, operation) {
		    this.onSongLoaded(record);
		},
		callback : function(record, operation) {
		    // do something whether the load succeeded
		    // or failed
		}
	    });
	} else {
	    this.onSongLoaded(new Songserver.model.Lied());
	}
    },

    /**
     * Takes the given song and prepares the GUI according to this song.
     * 
     * @param {Songserver.model.Song}
     *                song
     */
    onSongLoaded : function(song) {
	this.song = song;
	this.loadRecord(this.song);
	this.postLoadUserInformation();
	this.createAndAddSongbookGrid();
	this.addChangeListeners();
	this.fireEvent("songLoaded", this.song);
    },

    postLoadUserInformation : function() {
	this.song.getUser({
	    success : function(record, operation) {
		this.down("#user").setValue(record.get("firstname") + " " + record.get("lastname") + " (" + record.get("email") + ")");
		// set the value as init value so we don't loose
		// during a form
		// reset.
		this.down("#user").initValue();
	    },
	    scope : this
	});
    },

    createAndAddSongbookGrid : function() {
	bookentriesStore = this.song.numberInBooks();
	this.songbookGrid = Ext.create('Ext.grid.Panel', {
	    // preventHeader : true,
	    store : bookentriesStore,
	    columns : {
		defaults : {
		    // Workaround for 6.2.0:
		    // https://www.sencha.com/forum/showthread.php?325776-Upgrading-from-6-0-2-to-6-2-0-has-made-all-our-grids-right-align/
		    align : 'left'
		},
		items : [ {
		    header : 'Kürzel',
		    dataIndex : 'mnemonic'
		}, {
		    header : 'Nummer',
		    dataIndex : 'Liednr',
		    editor : {}
		}, {
		    header : 'Liederbuch',
		    dataIndex : 'Buchname',
		    flex : 1
		} ]
	    },
	    height : 276,
	    flex : 1,
	    padding : '0px 0px 0px 5px',
	    autoLoad : 'true',
	    plugins : Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit : 1,
		listeners : {
		    edit : function(editor, e, eOpts) {
			if (e.record.dirty) {
			    this.showToolbarItems();
			}
		    },
		    scope : this
		}
	    })
	});

	this.add(this.songbookGrid);
    },

    // this function adds change listeners to the form and the
    // grid
    // in order to show the fbar so that the user can see the
    // hidden buttons
    // "save" and "discard changes"
    addChangeListeners : function() {
	this.getForm().addListener("dirtychange", function(form, dirty, eOpts) {
	    if (dirty) {
		this.showToolbarItems();
	    }
	}, this);

    },

    saveChanges : function() {
	this.up("songserver-songPanel").displayInfoMessage("Änderungen werden gespeichert. Bitte warten...");
	this.up("songserver-songPanel").setLoading(true);
	var form = this.getForm();
	var record = form.getRecord();
	this.song.set(form.getValues());

	this.song.save({
	    success : function(record, operation) {
		this.loadRecord(this.song);
		this.up("songserver-songPanel").displayInfoMessage("Änderungen am Lied gespeichert. Speichere Liedernummern...");

		// https://trello.com/c/WtjY4hle Ablegen der
		// neuen
		// NumberInSongbook-Einträge schlägt fehl bei
		// neuem Lied
		this.setLiedIdOnAllNumberInBookEntries(record.get("id"));

		this.saveNumberInBookEntries();
	    },
	    failure : function(record, operation) {
		this.handleSaveError('Fehler beim Speichern der Lied-Eigenschaften.');
		this.up("songserver-songPanel").setLoading(false);
	    },
	    scope : this
	});

    },

    setLiedIdOnAllNumberInBookEntries : function(lied_id) {
	this.songbookGrid.getStore().data.each(function(record) {
	    var currentIsDirtyState = record.dirty;
	    record.set("lied_id", lied_id);
	    // We don't mark the record as dirty if it isn't
	    // yet.
	    record.dirty = currentIsDirtyState;
	});
    },

    saveNumberInBookEntries : function() {
	var modifiedRecords = this.getDirtyNumberInBookEntries();
	if (modifiedRecords.length > 0) {
	    this.saveOrDeleteSingleRecord(modifiedRecords[0]);
	} else {
	    this.up("songserver-songPanel").displayInfoMessage("Änderungen am Lied gespeichert.");
	    this.up("songserver-songPanel").switchToEditMode(this.song);
	    this.songbookGrid.getStore().reload();
	    this.hideToolbarItems();
	    this.up("songserver-songPanel").setLoading(false);
	}
    },

    saveOrDeleteSingleRecord : function(record) {
	if (record.get("Liednr")) { // neither null or empty
	    this.saveSingleRecord(record);
	} else {
	    this.deleteSingleRecord(record);
	}
    },

    saveSingleRecord : function(record) {
	record.save({
	    success : function(record, operation) {
		// Hack: The commit call is necessary in case of
		// a create
		// operation. The triangle flag (markDirty)
		// does not disappear because internally the
		// framework calls
		// it with modifiedFieldNames empty which
		// is not as we would expect it.
		record.commit();
		this.saveNumberInBookEntries();
	    },
	    failure : function(record, operation) {
		this.handleSaveError(operation.getError());
		this.up("songserver-songPanel").setLoading(false);
	    },
	    scope : this
	});
    },

    deleteSingleRecord : function(record) {
	record.erase({
	    success : function(record, operation) {
		this.saveNumberInBookEntries();
	    },
	    failure : function(record, operation) {
		this.handleSaveError('Fehler beim Speichern. Bitte melde diesen Fehler an ' //
			+ 'lieder@adoray.ch mit der Info, dass das Entfernen einer Liednummer fehlgeschlagen ist.');
		this.up("songserver-songPanel").setLoading(false);
	    },
	    scope : this
	});
    },

    getDirtyNumberInBookEntries : function() {
	return this.songbookGrid.getStore().data.filterBy(function(item) {
	    return item.dirty === true;
	}).items;
    },

    handleSaveError : function(message) {
	this.up("songserver-songPanel").displayErrorMessage("Fehler beim Speichern.");
	Ext.Msg.show({
	    title : 'Fehler beim Speichern',
	    msg : message,
	    buttons : Ext.Msg.OK,
	    icon : Ext.Msg.ERROR,
	    scope : this
	});
    },

    resetChanges : function() {
	this.getForm().reset();

	// reject the changes in all grid records
	var modifiedRecords = this.getDirtyNumberInBookEntries();
	Ext.Array.each(modifiedRecords, function(value) {
	    value.reject();
	});

	this.hideToolbarItems();
    },

    showToolbarItems : function() {
	var toolbar = this.child("#footerBar");
	toolbar.show();
    },

    hideToolbarItems : function() {
	var toolbar = this.child("#footerBar");
	toolbar.hide();
    }
});