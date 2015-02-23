Ext.define("Songserver.view.NotenDisplayfield", {
    extend : 'Ext.form.field.Display',
    alias : 'widget.songserver-notenDisplayfield',

    setValue : function(value) {
	if (value > 0) {
	    value = '<a href="src/ext-rest-interface.php/file/' + value + '" target="_blank">Noten anzeigen</a>';
	} else {
	    value = 'keine Noten vorhanden';
	}
	return this.callParent(arguments);
    }
});