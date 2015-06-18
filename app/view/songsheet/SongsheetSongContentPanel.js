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

    createAndAddSongsheetThumbnailPanels : function() {
	var songPanel = this.up("songserver-songPanel");
	if (songPanel.fileStore.count() > 0) {
	    var id = songPanel.fileStore.getAt(0).get("id");
	    this.add(this.createPdfThumbnail(id));
	} else {
	    this.add(this.createNoSongsheetAvailableThumbnail());
	    //this.add(this.createUploadThumbnail());
	}
    },

    privates : {
	createPdfThumbnail : function(fileId) {
	    return Ext.create('Songserver.view.songsheet.SongsheetThumbnailPanel', {
		html : '<a href="src/ext-rest-interface.php/file?filter=[{%22property%22:%22filemetadata_id%22,%22value%22:' + fileId + '}]" target="_blank"><div>' + //
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
	}
    }

});