Ext.define("Songserver.view.NotenDisplayfield", {
    extend : 'Ext.form.field.Display',
    alias : 'widget.songserver-notenDisplayfield',

    setValue : function(value) {
	if (value > 0) {
	    value = '<a href="src/ext-rest-interface.php/file/' + value + '" target="_blank">Noten anzeigen</a>';
	} else if(value == 0 || value == ""){
	    value = 'keine Noten vorhanden';
	}else{
	    // leave as is. For example if form is resetted, we already have a link.
	}
	return this.callParent(arguments);
    }
});