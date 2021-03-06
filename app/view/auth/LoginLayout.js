/**
 * Sets up the layout of the application. Note that there exists another
 * approach using Ext.application() to create an app as well. See the MVC
 * article in the ext docs for details. Philipp Jenni
 */

// create namespace
Ext.namespace('Songserver.view.auth');

Ext.define('Songserver.view.auth.LoginLayout', {
    extend : 'Ext.panel.Panel',
    requires : [ 'Songserver.view.auth.LoginForm', //
    'Songserver.view.auth.RegistrationForm' ],

    initComponent : function() {
	Ext.apply(this, {
	    layout : 'border',
	    items : [ {
		itemId : 'mainpanel',
		region : 'center',
		bodyStyle : 'background-color: rgb(50, 83, 100);',
		layout : {
		    type : 'vbox',
		    align : 'center',
		    pack : 'center'
		},
		items : [ {
		    xtype : 'songserver-loginform'
		} ]
	    } ]
	});

	this.callParent(arguments);
    },

    loadPanel : function(panelClass, options) {
	this.query("#mainpanel")[0].removeAll()
	this.query("#mainpanel")[0].add(Ext.create(panelClass, options));
    }
});