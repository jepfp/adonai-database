/**
 * Application Context where session based properties are stored and the
 * configuration is stored.
 */

Ext.namespace('Songserver');

Ext.define('Songserver.AppContext', {
    singleton : true,

    showQuestionBeforeSaveSongtableNumberEdit : true,

    /*
     * The viewport, to which either the login or main application panel is
     * added.
     */
    viewport : null,

    /*
     * The current main layout with border layout, which holds the other view
     * panels (navigation, table etc.)
     */
    mainLayout : null,

    isLoggedIn : function() {
	if (SCOTTY_CLIENT_CONFIGURATION.user != null) {
	    return true;
	} else {
	    return false;
	}
    },

    setLoggedIn : function(userInfo) {
	SCOTTY_CLIENT_CONFIGURATION.user = userInfo;
    }
});
