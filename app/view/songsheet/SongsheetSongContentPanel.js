Ext.namespace('Songserver.view');

Ext.define('Songserver.view.songsheet.SongsheetSongContentPanel', {
    extend : 'Songserver.view.SongContentPanel',
    requires : [ 'Songserver.view.songsheet.SongsheetThumbnailPanel' ],
    alias : 'widget.songserver-songsheetSongContentPanel',
    title : 'Noten',
    layout : {
	type : 'hbox'
    },

    createAndAddSongsheetThumbnailPanels : function() {
	var songPanel = this.up("songserver-songPanel");
	var song = songPanel.getSong();
	var fileId = song.get("file_id");
	if (fileId > 0) {
	    this.add(this.createPdfThumbnail(fileId));
	} else {
	    this.add(this.createNoSongsheetAvailableThumbnail());
	}
    },

    privates : {
	createPdfThumbnail : function(fileId) {
	    return Ext.create('Songserver.view.songsheet.SongsheetThumbnailPanel', {
		html : '<a href="src/ext-rest-interface.php/file/' + fileId + '" target="_blank"><div>' + //
		'<img src="resources/images/pdf_icon.png" height="80px" />' + //
		'<br>Noten anzeigen.</div></a>'
	    });
	},

	createNoSongsheetAvailableThumbnail : function() {
	    return Ext.create('Songserver.view.songsheet.SongsheetThumbnailPanel', {
		html : '<div><img src="resources/images/pdf_icon_no_pdf.png" height="80px" />' + //
		'<br>keine Noten vorhanden</div>'
	    });
	}
    }

});