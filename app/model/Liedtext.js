Ext.namespace('Songserver.model');

Ext.define('Songserver.model.Liedtext', {
    extend : 'Ext.data.Model',

    fields : [ {
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
    // associations : [ {
    // type : 'belongsTo',
    // model : 'Songserver.model.Refrain',
    // foreignKey : 'refrain_id',
    // getterName : 'getRefrain'
    // } ],

    // We have a separate store with all refrains inside the Song view. We hold
    // the reference to the correct refrain here.
    refrainInRefrainStore : null,

    setRefrainInStore : function(refrain) {
	var refrainId = 0;
	if (refrain != null) {
	    refrainId = refrain.get("id");
	}
	this.refrainInRefrainStore = refrain;
	if (refrainId !== this.get("refrain_id")) {
	    // only update if not equals in order to prevent from setting it to
	    // dirty without need
	    this.set("refrain_id", refrainId);
	}
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