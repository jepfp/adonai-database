Ext.require([ 'Ext.direct.*' ]);

Ext.application({
    name : 'Songserver',
    views : [ 'Viewport', 'Layout', 'auth.LoginLayout', 'LiedView' ],
    models : [ 'Lied', 'Rubrik', 'LiedView', 'Liederbuch', 'NumberInBook', 'Liedtext', 'Refrain', 'User', 'Base' ],
    controllers : [ 'auth.Authenticate' ],
    launch : function() {
	Ext.direct.Manager.addProvider(REMOTING_API);

	Ext.get('appLoadingMessage').fadeOut({
	    remove : true,
	    duration : 500
	});

	// The tool tips are to small.
	// This quick fix is a temporary solution.
	// http://www.sencha.com/forum/showthread.php?260106-Tooltips-on-forms-and-grid-are-not-resizing-to-the-size-of-the-text/page2
	delete Ext.tip.Tip.prototype.minWidth;
	if (Ext.isIE10) {
	    Ext.override(Ext.tip.Tip, {
		componentLayout : {
		    type : 'fieldset',
		    getDockedItems : function() {
			return [];
		    }
		}
	    });
	}
	Ext.QuickTips.init();

	Ext.create('Songserver.view.Viewport', {});
    }
});
