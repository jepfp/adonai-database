// create namespace
Ext.namespace('Songserver');

// The following libraries are not required by the Songserver code but
// maybe required by ext and not declared. That's why we declare them here.
Ext.require("Ext.layout.container.Border");
Ext.require("Ext.grid.plugin.CellEditing");
Ext.require("Ext.layout.component.FieldSet");

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