Ext.define('Songserver.model.Base', {
    extend : 'Ext.data.Model',
    requires : [ 'Ext.data.proxy.Rest' ],

    fields : [ {
	name : 'id',
	type : 'int'
    } ],

    schema : {
	namespace : 'Songserver.model',

	proxy : {
	    // first letter not capital
	    url : 'src/ext-rest-interface.php/{entityName:uncapitalize}',
	    type : "rest",
	    reader : {
		type : 'json',
		rootProperty : 'data',
		totalProperty : 'totalCount',
		messageProperty : 'message'
	    }
	}
    }
});