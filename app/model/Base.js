Ext.define('Songserver.model.Base', {
    extend : 'Ext.data.Model',

    fields : [ {
	name : 'id',
	type : 'int'
    } ],

    schema : {
	namespace : 'Songserver.model',

	proxy : {
	    url : 'src/ext-rest-interface.php/{entityName:lowercase}',
	    type : "rest",
	    reader : {
		type : 'json',
		rootProperty : 'data',
		totalProperty : 'totalCount'
	    }
	}
    }
});