Ext.namespace('Songserver.view');

Ext.define('Songserver.view.songsheet.SongsheetSongContentPanel', {
    extend : 'Songserver.view.SongContentPanel',
    requires : [ 'Songserver.view.songsheet.SongsheetThumbnailPanel', 'Songserver.view.songsheet.SongsheetUploadThumbnailPanel' ],
    alias : 'widget.songserver-songsheetSongContentPanel',
    title : 'Noten',
    layout : {
	type : 'hbox',
	align : 'stretch'
    },

    dockedItems : {
	xtype : 'toolbar',
	dock : 'bottom',
	style : {
	    backgroundColor : '#f5f5f5'
	},
	items : [ {
	    itemId : 'removeSongsheet',
	    xtype : 'button',
	    hidden : true,
	    icon : 'resources/images/silk/icons/page_delete.png',
	    text : 'Noten löschen',
	    listeners : {
		click : function(button, e) {
		    this.up('songserver-songsheetSongContentPanel').deleteSongsheet(true);
		}
	    }
	} ]
    },
    createAndAddSongsheetThumbnailPanels : function() {
	var songsheet = this.getFirstSongsheetItem();
	this.removeAll();
	if (songsheet != null) {
	    var id = songsheet.get("id");
	    this.add(this.createPdfThumbnail(id));
	    this.down('#removeSongsheet').setVisible(true);
	} else {
	    this.add(this.createNoSongsheetAvailableThumbnail());
	    this.add(this.createUploadThumbnail());
	    this.down('#removeSongsheet').setVisible(false);
	}
    },

    privates : {
	createPdfThumbnail : function(fileId) {
	    return Ext.create('Songserver.view.songsheet.SongsheetThumbnailPanel', {
		html : '<a href="src/ext-rest-interface.php/file?filter=[{%22property%22:%22filemetadata_id%22,%22value%22:' + fileId
			+ '}]" target="_blank"><div>' + //
			'<img src="resources/images/pdf_icon.png" height="80px" />' + //
			'<br>Noten anzeigen.</div></a>'
	    });
	},

	createNoSongsheetAvailableThumbnail : function() {
	    return Ext.create('Songserver.view.songsheet.SongsheetThumbnailPanel', {
		html : '<div><img src="resources/images/pdf_icon_no_pdf.png" height="80px" />' + //
		'<br>keine Noten vorhanden</div>'
	    });
	},

	createUploadThumbnail : function() {
	    return Ext.create('Songserver.view.songsheet.SongsheetUploadThumbnailPanel', {
		flex : 1
	    });
	},

	getFirstSongsheetItem : function() {
	    var songPanel = this.up("songserver-songPanel");
	    return songPanel.fileStore.getAt(0);
	},

	deleteSongsheet : function(promptFirst) {
	    if (promptFirst === undefined) {
		promptFirst = true
	    }

	    if (promptFirst) {
		var mbox = Ext.Msg.show({
		    title : 'Löschen bestätigen',
		    msg : 'Möchtest du die Noten zu diesem Lied wirklich löschen? Dieser Schritt kann nicht rückgängig gemacht werden.',
		    buttons : Ext.Msg.YESNO,
		    icon : Ext.Msg.QUESTION,
		    fn : function(btn, text) {
			if (btn == 'yes') {
			    this.deleteSongsheet(false);
			}
		    },
		    scope : this
		});
	    } else {
		var songsheet = this.getFirstSongsheetItem();
		songsheet.erase({
		    scope : this,
		    failure : function(record, operation) {
			Ext.Msg.alert("Fehler beim Löschen", "Die Noten konnten nicht gelöscht werden. "
				+ "Bitte informiere den Website-Verantwortlichen über diesen Fehler.");
		    },
		    success : function(record, operation) {
			this.createAndAddSongsheetThumbnailPanels();
			this.up("songserver-songPanel").displayInfoMessage("Noten gelöscht.");
		    }
		});
	    }
	}
    }

});