/**
 * View and edit the attributes of one single refrain. This panel goes into the
 * SongtextView panel. Philipp Jenni
 */
Ext.namespace('Songserver.view');


Ext.define('Songserver.view.RefrainFormPanel', {
    extend : 'Songserver.view.SongtextFormPanel',
    requires : [ 'Songserver.view.SongtextFormPanel' ],
    alias : 'widget.songserver-refrainFormPanel',

    /**
     * The name of the content field which will be the attribute key for the
     * form that is sent to the server (e. g. verse / refrain).
     * 
     * @type String
     */
    songtextFieldName : "Refrain"
});