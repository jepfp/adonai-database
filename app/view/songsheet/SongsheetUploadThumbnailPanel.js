Ext.namespace('Songserver.view');

Ext.define('Songserver.view.songsheet.SongsheetUploadThumbnailPanel', {
    extend : 'Ext.form.Panel',
    alias : 'widget.songserver-songsheetUploadThumbnailPanel',
    bodyCls : 'songsheetThumbnailPanel',
    style : {
	margin : '5px'
    },

    layout : 'center',
    items : [ {
	xtype : 'container',
	items : [ {
	    bodyCls : 'uploadDrop',
	    xtype : 'filefield',
	    buttonOnly : true,
	    hideLabel : true,
	    name : 'songsheetUpload',
	    buttonText : 'PDF-Noten hochladen...',
	    listeners : {
		'change' : function(fb, v) {
		    var form = this.up('songserver-songsheetUploadThumbnailPanel').getForm();
		    form.submit({
			method : 'POST',
			url : 'index.php',
			waitMsg : 'Noten werden hochgeladen. Bitte warten...',
			success : function(fp, o) {
			    msg('Success', 'Processed file "' + o.result.file + '" on the server');
			}
		    });
		}
	    }
	} ]
    } ]

});