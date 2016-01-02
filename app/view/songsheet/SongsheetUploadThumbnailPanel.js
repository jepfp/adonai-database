Ext.namespace('Songserver.view');

Ext.define('Songserver.view.songsheet.SongsheetUploadThumbnailPanel', {
    extend : 'Ext.form.Panel',
    alias : 'widget.songserver-songsheetUploadThumbnailPanel',
    bodyCls : 'songsheetThumbnailPanel',
    style : {
	margin : '5px'
    },
    defaultListenerScope : true,

    layout : 'center',
    items : [ {
	xtype : 'container',
	items : [ {
	    bodyCls : 'uploadDrop',
	    xtype : 'filefield',
	    buttonOnly : true,
	    hideLabel : true,
	    name : 'file',
	    buttonText : 'PDF-Noten hochladen...',
	    listeners : {
		'change' : 'onChange'
	    }
	} ]
    } ],

    onChange : function(fb, v) {
	var form = this.getForm();
	form.submit({
	    method : 'POST',
	    url : 'src/ext-rest-interface.php/file',
	    params : {
		lied_id : this.determineLiedId()
	    },
	    waitMsg : 'Noten werden hochgeladen. Bitte warten...',
	    success : this.onFormSubmitSuccessful,
	    failure : this.onFormSubmitFailure,
	    scope : this
	});
    },

    determineLiedId : function() {
	var songPanel = this.up("songserver-songPanel");
	return songPanel.songId;
    },

    onFormSubmitSuccessful : function(form, action) {
	var songPanel = this.up("songserver-songPanel");
	songPanel.fileStore.load({
	    scope : this,
	    callback : function(records, operation, success) {
		this.up('songserver-songsheetSongContentPanel').createAndAddSongsheetThumbnailPanels();
		if (success) {
		    songPanel.displayInfoMessage("Datei erfolgreich hochgeladen.");
		} else {
		    songPanel.displayErrorMessage("Beim Hochladen der Datei ist ein Fehler aufgetreten. Bitte informiere uns via lieder@adoray.ch.");
		}
	    }
	});
    },

    onFormSubmitFailure : function(form, action) {
	var songPanel = this.up("songserver-songPanel");
	songPanel.showSaveErrorMessage(action.result.message);
	songPanel.fileStore.load({
	    scope : this,
	    callback : function(records, operation, success) {
		this.up('songserver-songsheetSongContentPanel').createAndAddSongsheetThumbnailPanels();
	    }
	});
    }

});