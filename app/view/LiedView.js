/**
 * View for all songs Philipp Jenni
 */

Ext.namespace('Songserver.view');

Ext.define('Songserver.view.LiedView', {
    extend : 'Ext.grid.Panel',
    requires : [ 'Songserver.view.Song', 'Ext.form.field.ComboBox', 'Ext.grid.column.Date' ],

    liederbuch : null,
    // the currently selected reocrd or null if no record is
    // selected
    selectedRecord : null,

    tableViews : {
	"tableOfContents" : [ {
	    header : 'Nummer',
	    dataIndex : 'Liednr',
	    editor : {
		xtype : 'textfield',
		allowBlank : true
	    }
	}, {
	    header : 'Titel',
	    dataIndex : 'Titel',
	    flex : 1
	}, {
	    header : 'Rubrik',
	    dataIndex : 'Rubrik'
	}, {
	    header : 'Tonart',
	    dataIndex : 'tonality'
	} ],
	"lastChanges" : [ {
	    header : 'Geändert am',
	    dataIndex : 'updated_at',
	    width : 145
	}, {
	    header : 'Benutzer',
	    dataIndex : 'email',
	    width : 250
	}, {
	    header : 'Nummer',
	    dataIndex : 'Liednr',
	    editor : {
		xtype : 'textfield',
		allowBlank : true
	    }
	}, {
	    header : 'Titel',
	    dataIndex : 'Titel',
	    flex : 1
	} ]
    },

    initComponent : function() {
	var store = this.getSongStore();
	Ext.apply(this, {
	    title : SCOTTY_CLIENT_CONFIGURATION.projectTitle + " - Willkommen " + SCOTTY_CLIENT_CONFIGURATION.user.firstname,
	    store : store,
	    disableSelection : false,
	    listeners : {
		itemdblclick : function(view, record, item, index, e) {
		    this.editSong(record.get("id"));
		},
		selectionchange : function(panel, selected, eOpts) {
		    if (selected.length == 1) {
			this.selectedRecord = selected[0];
			this.down("#editSong").enable();
			this.down("#deleteSong").enable();
		    } else {
			this.selectedRecord = null;
			this.down("#editSong").disable();
			this.down("#deleteSong").disable();
		    }
		},
		scope : this
	    },
	    tbar : {
		enableOverflow : true,
		itemId : "tbar",
		items : [ {
		    name : 'quicksearch',
		    itemId : 'quicksearch',
		    xtype : 'textfield',
		    inputType : 'search',
		    emptyText : 'Suchbegriff oder Nr eingeben...',
		    width : 400,
		    listeners : {
			scope : this,
			change : function(textfield, newValue, oldValue) {
			    this.delayedSearch.delay(750, null, this, [ newValue ]);
			}
		    }
		}, {
		    itemId : 'addSong',
		    xtype : 'button',
		    icon : 'resources/images/silk/icons/add.png',
		    text : 'Hinzufügen',
		    listeners : {
			scope : this,
			click : function(button, e) {
			    this.createSong();
			}
		    }
		}, {
		    itemId : 'editSong',
		    xtype : 'button',
		    icon : 'resources/images/silk/icons/pencil.png',
		    text : 'Bearbeiten',
		    disabled : true,
		    listeners : {
			scope : this,
			click : function(button, e) {
			    this.editSong(this.selectedRecord.get("id"));
			}
		    }
		}, {
		    itemId : 'deleteSong',
		    xtype : 'button',
		    icon : 'resources/images/silk/icons/cross.png',
		    text : 'Löschen',
		    disabled : true,
		    listeners : {
			scope : this,
			click : function(button, e) {
			    this.deleteSong(this.selectedRecord);
			}
		    }
		}, "->", {
		    itemId : 'viewTableOfContents',
		    xtype : 'button',
		    icon : 'resources/images/silk/icons/book.png',
		    text : 'Inhaltsverzeichnis',
		    listeners : {
			scope : this,
			click : function(button, e) {
			    var currentView = "tableOfContents";
			    this.reconfigure(null, this.tableViews[currentView]);
			    this.store.currentTableView = currentView;
			    this.store.sort("LiedNr", "ASC");
			}
		    }
		}, {
		    itemId : 'viewLastChanges',
		    xtype : 'button',
		    icon : 'resources/images/silk/icons/clock_edit.png',
		    text : 'Letzte Änderungen',
		    listeners : {
			scope : this,
			click : function(button, e) {
			    var currentView = "lastChanges";
			    this.reconfigure(null, this.tableViews[currentView]);
			    this.store.currentTableView = currentView;
			    this.store.sort("updated_at", "DESC");
			}
		    }
		} ]
	    },
	    columns : this.tableViews[store.currentTableView],
	    plugins : [ Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit : 1,
		listeners : {
		    edit : this.onCellEditingEdit,
		    scope : this
		}
	    }) ]
	});

	this.store.load();

	Songserver.view.LiedView.superclass.initComponent.apply(this, arguments);

	this.setQuicksearchFieldFromCurrentStoreFilter();
    },

    setQuicksearchFieldFromCurrentStoreFilter : function() {
	var value = this.store.proxy.extraParams.quicksearch;
	if (value) {
	    this.down("#quicksearch").setValue(value);
	}
    },

    /**
     * Opens the song panel and loads the song with the given id into that panel
     * for editing.
     * 
     * @param {int}
     *                id Internal song id
     */
    editSong : function(id) {
	Songserver.AppContext.mainLayout.loadPanel("Songserver.view.Song", {
	    songId : id
	});
    },

    /**
     * Opens the song panel in order to create a new song.
     */
    createSong : function() {
	Songserver.AppContext.mainLayout.loadPanel("Songserver.view.Song", {});
    },

    /**
     * Deletes the given song
     * 
     * @param {Songserver.model.Song}
     *                song Song to delete
     * @param {boolean}
     *                PromptFirst If set to true, the user will be asked first.
     *                Default: true
     */
    deleteSong : function(song, promptFirst) {
	if (promptFirst === undefined) {
	    promptFirst = true
	}

	if (promptFirst) {
	    var mbox = Ext.Msg.show({
		title : 'Lied löschen?',
		msg : 'Möchtest du das Lied "' + song.get("Titel") + '" wirklich löschen?',
		buttons : Ext.Msg.YESNO,
		icon : Ext.Msg.QUESTION,
		fn : function(btn, text) {
		    if (btn == 'yes') {
			this.deleteSong(song, false);
		    }
		},
		scope : this
	    });
	} else {
	    song.erase({
		scope : this,
		failure : function(record, operation) {
		    Ext.Msg.alert("Fehler beim Löschen", "Das Lied konnte nicht gelöscht werden. "
			    + "Bitte informiere den Website-Verantwortlichen über diesen Fehler.");
		},
		success : function(record, operation) {
		    Songserver.AppContext.mainLayout.loadPanel("Songserver.view.LiedView");
		}
	    });
	}
    },

    getSongStore : function() {
	this.store = Ext.data.StoreManager.lookup('songView');
	if (this.store == null) {
	    this.store = Ext.create('Ext.data.Store', {
		storeId : 'songView',
		model : 'Songserver.model.LiedView',
		// As long as there is no buffered or paged view we leave this.
		// buffered : true,
		// leadingBufferZone : 300,
		pageSize : 10000,
		remoteSort : true,
		// defines which columns shall be displayed. e. g.
		// tableOfContents or lastChanges
		currentTableView : "tableOfContents",

		listeners : {
		    load : function(store, records, successful, operation, options) {
			this.determineCurrentLiederbuch();
		    },
		    scope : this
		}
	    });
	}
	return this.store;
    },

    determineCurrentLiederbuch : function() {
	SessionInfoProvider.getCurrentLiederbuchId(function(result, e) {
	    Songserver.model.Liederbuch.load(result, {
		success : function(liederbuch) {
		    this.liederbuch = liederbuch;
		},
		scope : this
	    });
	}, this);
    },

    delayedSearch : new Ext.util.DelayedTask(function(searchValue) {
	var currentValue = this.store.proxy.extraParams.quicksearch;
	if (currentValue == searchValue) {
	    return;
	}
	this.store.proxy.extraParams = {
	    "quicksearch" : searchValue
	};
	this.store.load();
    }, this),

    /**
     * Eventhandler after a cell has been edited (even if no changes were made).
     * 
     * @param {}
     *                editor
     * @param {}
     *                e
     */
    onCellEditingEdit : function(editor, e) {
	if (!e.record.dirty) {
	    return;
	}

	if (!Songserver.AppContext.showQuestionBeforeSaveSongtableNumberEdit) {
	    this.saveQuickEditChanges(e.record);
	    return;
	}

	Ext.Msg.show({
	    title : 'Lied ändern?',
	    msg : 'Möchtest du die Nummer des Liedes "' + e.record.get("Titel") + '" wirklich von "' + e.originalValue + '" nach "' + e.value + '" ändern?',
	    buttons : Ext.Msg.YESNO,
	    icon : Ext.Msg.QUESTION,
	    editEvent : e,
	    editEditor : editor,
	    fn : function(btn, text, opt) {
		var record = opt.editEvent.record;
		if (btn == 'yes') {
		    this.saveQuickEditChanges(record);
		    Songserver.AppContext.showQuestionBeforeSaveSongtableNumberEdit = false;
		} else {
		    record.set("nr", opt.editEvent.originalValue);
		}
	    },
	    scope : this
	});
    },

    /**
     * Saves the changes of the given record to the server and adds the
     * "quickEdit = true" flag to signalize that the record was changed inside
     * the table.
     * 
     * @param {Ext.data.Model}
     *                record record to save
     */
    saveQuickEditChanges : function(record) {
	this.store.getProxy().setExtraParam("quickEdit", true);
	this.store.sync({
	    success : function(batch, options) {
	    },
	    failure : function(batch, options) {
		Ext.Msg.show({
		    title : 'Fehler beim Speichern',
		    msg : 'Fehler beim Speichern. Eventuell wird die Nummer bereits für ein anderes ' + 'Lied in diesem Liederbuch verwendet.'
			    + '<br>Bitte versuche es mit einer anderen Nummer.',
		    buttons : Ext.Msg.OK,
		    icon : Ext.Msg.ERROR,
		    scope : this
		});

	    },
	    scope : this
	});
    }
});