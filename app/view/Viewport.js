// create namespace
Ext.namespace('Songserver');

// Workaround for bug with 6.2.0 (layout class is not available in production /
// testing mode.
Ext.require("Ext.layout.container.boxOverflow.Menu");

Ext.define('Songserver.view.Viewport', {
    extend : 'Ext.container.Viewport',
    requires : [ 'Songserver.view.Layout', 'Songserver.view.auth.LoginLayout', 'Songserver.AppContext' ],

    initComponent : function() {
	Ext.apply(this, {
	    layout : 'fit'
	});

	this.callParent(arguments);

	Songserver.AppContext.viewport = this;
	if (Songserver.AppContext.isLoggedIn()) {
	    this.loadApplicationLayout();
	} else {
	    this.loadLoginLayout();
	}

    },

    loadLoginLayout : function() {
	this.loadPanel('Songserver.view.auth.LoginLayout', {});
    },

    loadApplicationLayout : function() {
	this.loadPanel('Songserver.view.Layout', {});
    },

    loadPanel : function(panelClass, options) {
	this.setLoading(true);
	this.removeAll()
	Songserver.AppContext.mainLayout = this.add(Ext.create(panelClass, options));
	this.setLoading(false);
    }
});