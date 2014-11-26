Ext.namespace('Songserver.view.auth');

Ext.define('Songserver.controller.auth.Authenticate', {
	extend : 'Ext.app.Controller',

	views : [ 'auth.LoginForm' ],

	init : function() {
		console.log("init of controller called.");
	}
});