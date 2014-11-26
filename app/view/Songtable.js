/**
 * View for all songs Philipp Jenni
 */

/**
 * config parameters loadLiederbuch - {int} id of a new liederbuch, to request
 * the songs for
 */

Ext.namespace('Songserver.view');

Ext.define('Songserver.view.SongtableOLD', {
	extend : 'Ext.grid.Panel',
	requires : ['Ext.PagingToolbar', 'Songserver.view.Song',
			'Ext.form.field.ComboBox'],

	numOfRecords : 50,
	// {Array} the currently active liederbuch with the keys "id", "buchname"
	// and "description"
	liederbuch : null,
	// the currently selected reocrd or null if no record is selected
	selectedRecord : null,

	initComponent : function() {
		Ext.apply(this, {
					title : 'Lieder',
					store : this.getSongStore(),
					loadMask : true,
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
						items : [{
							name : 'quicksearch',
							xtype : 'textfield',
							fieldLabel : 'Suche',
							labelWidth : 50,
							emptyText : 'Suchbegriff oder Nr eingeben...',
							width : 250,
							listeners : {
								scope : this,
								change : function(textfield, newValue, oldValue) {
									this.delayedSearch.delay(750, null, this,
											[newValue]);
								}
							}
						}, "-", {
							itemId : 'editSong',
							xtype : 'button',
							icon : 'resources/images/silk/icons/pencil.png',
							text : 'Lied bearbeiten',
							disabled : true,
							listeners : {
								scope : this,
								click : function(button, e) {
									this
											.editSong(this.selectedRecord
													.get("id"));
								}
							}
						}, {
							itemId : 'deleteSong',
							xtype : 'button',
							icon : 'resources/images/silk/icons/cross.png',
							text : 'Lied löschen',
							disabled : true,
							listeners : {
								scope : this,
								click : function(button, e) {
									this.deleteSong(this.selectedRecord);
								}
							}
						}, "-", {
							itemId : 'addSong',
							xtype : 'button',
							icon : 'resources/images/silk/icons/add.png',
							text : 'Lied hinzufügen',
							listeners : {
								scope : this,
								click : function(button, e) {
									this.createSong();
								}
							}
						}]
					},
					columns : [{
								header : 'Nummer',
								dataIndex : 'nr',
								editor : {
									xtype : 'textfield',
									allowBlank : true
								}
							}, {
								header : 'Titel',
								dataIndex : 'title',
								flex : 1
							}, {
								header : 'Rubrik',
								dataIndex : 'category'
							}],
					plugins : [Ext.create('Ext.grid.plugin.CellEditing', {
								clicksToEdit : 1,
								listeners : {
									edit : this.onCellEditingEdit,
									scope : this
								}
							})],
					// create the toolbar on the bottom
					bbar : Ext.create('Ext.PagingToolbar', {
								store : this.store,
								displayInfo : true,
								beforePageText : "Seite",
								afterPageText : "von {0}",
								displayMsg : 'Zeige die Lieder {0} - {1} von {2}',
								emptyMsg : "Keine Lieder gefunden.",
								items : {
									name : 'numOfRecords',
									xtype : 'combobox',
									fieldLabel : 'Anzahl Resultate',
									width : 175,
									queryMode : 'local',
									value : this.numOfRecords.toString(),
									store : {
										fields : ['text'],
										data : [{
													"text" : "50"
												}, {
													"text" : "100"
												}, {
													"text" : "200"
												}, {
													"text" : "5000"
												}]
									},
									listeners : {
										'select' : function(box, value, options) {
											this.numOfRecordsChanged(box
													.getValue());
										},
										'blur' : function(field, options) {
											this.numOfRecordsChanged(field
													.getValue());
										},
										scope : this
									}

								}
							})
				});

		if (this.loadLiederbuch != null) {
			this.store.load({
						params : {
							liederbuchId : this.loadLiederbuch
						}
					});
			this.loadLiederbuch = null;
		} else {
			this.store.load();
		}

		Songserver.view.Songtable.superclass.initComponent.apply(this,
				arguments);
	},

	/**
	 * Opens the song panel and loads the song with the given id into that panel
	 * for editing.
	 * 
	 * @param {int}
	 *            id Internal song id
	 */
	editSong : function(id) {
		Songserver.AppContext.mainLayout.loadPanel("Songserver.view.Song", {
					songId : id
				});
	},

	/**
	 * Opens the song panel in order to edit a new song.
	 */
	createSong : function() {
		Songserver.AppContext.mainLayout.loadPanel("Songserver.view.Song", {
					songId : 0
				});
	},

	/**
	 * Deletes the given song
	 * 
	 * @param {Songserver.model.Song}
	 *            song Song to delete
	 * @param {boolean}
	 *            PromptFirst If set to true, the user will be asked first.
	 *            Default: true
	 */
	deleteSong : function(song, promptFirst) {
		if (promptFirst === undefined) {
			promptFirst = true
		}

		if (promptFirst) {
			var mbox = Ext.Msg.show({
						title : 'Lied löschen?',
						msg : 'Möchtest du das Lied "' + song.get("title")
								+ '" wirklich löschen?',
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
			song.destroy({
				scope : this,
				failure : function(record, operation) {
					Ext.Msg
							.alert(
									"Fehler beim Löschen",
									"Das Lied konnte nicht gelöscht werden. "
											+ "Bitte informiere den Website-Verantwortlichen über diesen Fehler.");
				},
				success : function(record, operation) {
					Songserver.AppContext.mainLayout
							.loadPanel("Songserver.view.Songtable");
				}
			});
		}
	},

	getSongStore : function() {
		if (this.store == null) {
			this.store = Ext.create('Ext.data.Store', {
				storeId : 'songtable',
				remoteSort : true,
				pageSize : this.numOfRecords,
				fields : [{
							name : 'id',
							type : 'int'
						}, {
							name : 'nr',
							type : 'string'
						}, {
							name : 'title',
							type : 'string'
						}, {
							name : 'category',
							type : 'string'
						}],
				proxy : {
					// url : 'symfony/web/webservice_dev.php/lied',
					url : 'sfWeb/index.php/lied',
					type : "symfonyProxy",
					reader : {
						type : 'json',
						root : 'songtable',
						totalProperty : 'totalCount'
					}
				},
				listeners : {
					load : function(store, records, successful, operation,
							options) {
						this
								.setLiederbuch(store.proxy.reader.jsonData.liederbuch);
					},
					scope : this
				}
			});
		}
		return this.store;
	},

	numOfRecordsChanged : function(newValue) {
		if (isNaN(newValue)) {
			return;
		}

		if (newValue != this.numOfRecords) {
			this.numOfRecords = newValue;
			this.store.pageSize = this.numOfRecords;
			this.store.loadPage(1);
		}
	},

	setLiederbuch : function(liederbuch) {
		this.liederbuch = liederbuch;
		this.setTitle("Lieder (" + this.liederbuch.bookname + ")");
	},

	delayedSearch : new Ext.util.DelayedTask(function(searchValue) {
				this.store.proxy.extraParams = {
					"quicksearch" : searchValue
				};
				this.store.loadPage(1);
			}, this),

	/**
	 * Eventhandler after a cell has been edited (even if no changes were made).
	 * 
	 * @param {}
	 *            editor
	 * @param {}
	 *            e
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
			msg : 'Möchtest du die Nummer des Liedes "' + e.record.get("title")
					+ '" wirklich von "' + e.originalValue + '" nach "'
					+ e.value + '" ändern?',
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
	 *            record record to save
	 */
	saveQuickEditChanges : function(record) {
		this.store.getProxy().setExtraParam("quickEdit", true);
		this.store.sync({
			success : function(batch, options) {
			},
			failure : function(batch, options) {
				Ext.Msg.show({
					title : 'Fehler beim Speichern',
					msg : 'Fehler beim Speichern. Eventuell wird die Nummer bereits für ein anderes '
							+ 'Lied in diesem Liederbuch verwendet.'
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