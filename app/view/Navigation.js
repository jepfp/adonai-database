/**
 * Navigation view Philipp Jenni
 */
Ext.namespace('Songserver.view');

Ext.define('Songserver.view.Navigation', {
    extend : 'Ext.panel.Panel',
    requires : [ 'Ext.layout.container.Accordion' ],
    alias : 'widget.songserver-navigation',

    initComponent : function() {
	Ext.apply(this, {
	    title : '',
	    layout : 'accordion',
	    defaults : {
		autoScroll : true
	    },
	    items : [ {
		title : 'Willkommen',
		loader : {
		    url : 'content/start.php',
		    renderer : 'html',
		    autoLoad : true
		},
		layout : 'fit'
	    }, {
		title : 'Liederbuch wählen',
		items : [ {
		    name : 'buchSelection',
		    xtype : 'combobox',
		    padding : 8,
		    width : 274,
		    valueField : 'id',
		    displayField : 'Buchname',
		    emptyText : 'Liederbuch wählen...',
		    forceSelection : true,
		    editable : false,
		    tpl : Ext.create('Ext.XTemplate', '<tpl for=".">', '<div class="x-boundlist-item"><b>{mnemonic}</b> {Buchname}</div>', '</tpl>'),
		    listConfig : {
			listeners : {
			    itemclick : function(list, record) {
				SessionInfoProvider.setCurrentLiederbuchId(record.get("id"), function(result, e) {
				    this.ownerCt.loadPanel("Songserver.view.LiedView");
				}, this);
			    },
			    scope : this
			}
		    },
		    store : {
			storeId : 'liederbuchAuswahl',
			model : 'Songserver.model.Liederbuch'
		    }
		}, {
		    border : false,
		    loader : {
			url : 'content/chooseLiederbuchHelp.php',
			renderer : 'html',
			autoLoad : true
		    }
		} ]
	    }, {
		title : 'Download',
		loader : {
		    url : 'content/download.php',
		    renderer : 'html',
		    autoLoad : true
		}
	    }, {
		title : 'Über das Programm',
		loader : {
		    url : 'content/about.php',
		    renderer : 'html',
		    autoLoad : true
		}
	    }, {
		title : 'Fehler melden',
		loader : {
		    url : 'content/reporting.php',
		    renderer : 'html',
		    autoLoad : true
		}
	    } ]
	});

	Songserver.view.Navigation.superclass.initComponent.apply(this, arguments);
    }
});