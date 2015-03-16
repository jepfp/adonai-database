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
    
    layout : 'border',
    items : [ {
	xtype : 'songserver-navigation',
	region : 'west',
	collapsible : true,
	split : true,
	width : 300
    }, {
	region : 'center',
	itemId : 'mainpanel',
	layout : 'fit'
    } ],

    initComponent : function() {
	this.callParent(arguments);

	this.loadPanel("Songserver.view.LiedView", null);

    },

    loadPanel : function(panelClass, options) {
	this.query("#mainpanel")[0].removeAll()
	var panel = Ext.create(panelClass, options);
	this.query("#mainpanel")[0].add(panel);
    }
});