Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Liedtext', {
    extend : 'Ext.data.Model',

    fields : [ {
	name : 'id',
	type : 'int'
    }, {
	name : 'Strophe',
	type : 'string'
    }, {
	name : 'refrain_id',
	type : 'int'
    }, {
	name : 'language_id',
	type : 'int'
    }, {
	name : 'lied_id',
	type : 'int'
    } ],

    // not needed for now as the refrains are loaded over a seperate store
    associations : [ {
	type : 'belongsTo',
	model : 'Songserver.model.Refrain',
	foreignKey : 'refrain_id',
	getterName : 'getRefrain'
    } ],

    // We have a separate store with all refrains inside the Song view. We hold
    // the reference to the correct refrain here.
    refrainInRefrainStore : null,

    //TODO: Noch nicht zu Ende gedacht. Was passiert beim Ändern des Refrains?
    setRefrainInStore : function(refrain) {
	this.refrainInRefrainStore = refrain;
    },

    getRefrainInStore : function() {
	return this.refrainInRefrainStore;
    },

    proxy : {
	url : 'src/ext-rest-interface.php/liedtext',
	type : "rest",
	reader : {
	    type : 'json',
	    root : 'data'
	}
    }

});