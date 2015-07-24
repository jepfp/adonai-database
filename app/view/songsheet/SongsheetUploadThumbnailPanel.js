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
	    success : function(fp, o) {
		msg('Success', 'Processed file "' + o.result.file + '" on the server');
	    }
	});
    },

    determineLiedId : function() {
	var songPanel = this.up("songserver-songPanel");
	return songPanel.songId;
    },

});