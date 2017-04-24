Ext.require([ 'Ext.direct.*' ]);

//Fixes bug with Firefox 52 and following versions
// See https://www.sencha.com/forum/showthread.php?336762-Examples-don-t-work-in-Firefox-52-touchscreen/page2
Ext.define('EXTJS_23846.Element', {
    override: 'Ext.dom.Element'
}, function(Element) {
    var supports = Ext.supports,
        proto = Element.prototype,
        eventMap = proto.eventMap,
        additiveEvents = proto.additiveEvents;

    if (Ext.os.is.Desktop && supports.TouchEvents && !supports.PointerEvents) {
        eventMap.touchstart = 'mousedown';
        eventMap.touchmove = 'mousemove';
        eventMap.touchend = 'mouseup';
        eventMap.touchcancel = 'mouseup';

        additiveEvents.mousedown = 'mousedown';
        additiveEvents.mousemove = 'mousemove';
        additiveEvents.mouseup = 'mouseup';
        additiveEvents.touchstart = 'touchstart';
        additiveEvents.touchmove = 'touchmove';
        additiveEvents.touchend = 'touchend';
        additiveEvents.touchcancel = 'touchcancel';

        additiveEvents.pointerdown = 'mousedown';
        additiveEvents.pointermove = 'mousemove';
        additiveEvents.pointerup = 'mouseup';
        additiveEvents.pointercancel = 'mouseup';
    }
});

Ext.define('EXTJS_23846.Gesture', {
    override: 'Ext.event.publisher.Gesture'
}, function(Gesture) {
    var me = Gesture.instance;

    if (Ext.supports.TouchEvents && !Ext.isWebKit && Ext.os.is.Desktop) {
        me.handledDomEvents.push('mousedown', 'mousemove', 'mouseup');
        me.registerEvents();
    }
});
// End of snippet

Ext.application({
    name : 'Songserver',
    views : [ 'Viewport', 'Layout', 'auth.LoginLayout', 'LiedView' ],
    models : [ 'Lied', 'Rubrik', 'LiedView', 'Liederbuch', 'NumberInBook', 'Liedtext', 'Refrain', 'FileMetadata', 'User', 'Base' ],
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
