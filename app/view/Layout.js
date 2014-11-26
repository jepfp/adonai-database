/**
 * Sets up the layout of the application. Note that there exists another
 * approach using Ext.application() to create an app as well. See the MVC
 * article in the ext docs for details. Philipp Jenni
 */

// create namespace
Ext.namespace('Songserver');

Ext.define('Songserver.view.Layout', {
    extend : 'Ext.panel.Panel',
    requires : [ 'Songserver.view.Navigation' ],

    initComponent : function() {
	Ext.apply(this, {
	    layout : 'border',
	    items : [ {
		region : 'north',
		bodyCls : 'applicationHeader',
		html : SCOTTY_CLIENT_CONFIGURATION.projectTitle + " - Willkommen " + SCOTTY_CLIENT_CONFIGURATION.user.firstname,
		border : false,
		split : false
	    }, {
		xtype : 'songserver-navigation',
		region : 'west',
		collapsible : true,
		split : false,
		width : 300
	    }, {
		region : 'center',
		itemId : 'mainpanel',
		border : false,
		layout : 'fit'
	    } ]
	});

	this.callParent(arguments);

	this.loadPanel("Songserver.view.LiedView", null);

    },

    loadPanel : function(panelClass, options) {
	this.query("#mainpanel")[0].removeAll()
	this.query("#mainpanel")[0].add(Ext.create(panelClass, options));
    }
});