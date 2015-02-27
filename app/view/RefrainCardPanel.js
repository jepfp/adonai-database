/**
 * A card panel which holds all the possible refrain for a song and allows the
 * user to choose one of the refrains by selecting the next / previos one or "no
 * refrain".
 */

Ext.namespace('Songserver.view');

/**
 * Events: - selectionChanged: Will be fired after another refrain has been
 * selected by the user.
 */
Ext.define('Songserver.view.RefrainCardPanel', {
    extend : 'Ext.panel.Panel',
    requires : [ "Ext.layout.container.Card" ],
    alias : 'widget.songserver-refrainCardPanel',

    // The refrain id which is currently assigned
    selectedRefrainId : null,
    songPanel : null,

    initComponent : function() {
	Ext.apply(this, {
	    preventHeader : true,
	    layout : 'card',
	    border : 'border-color: rgb(181, 184, 200);',
	    bodyStyle : 'border-color: rgb(181, 184, 200); padding: 3px;',
	    bbar : [ {
		itemId : 'move-prev',
		icon : 'resources/images/silk/icons/arrow_left.png',
		listeners : {
		    click : function() {
			this.navigate("prev");
		    },
		    scope : this
		}
	    }, '->', {
		itemId : 'move-next',
		icon : 'resources/images/silk/icons/arrow_right.png',
		listeners : {
		    click : function() {
			this.navigate("next");
		    },
		    scope : this
		}
	    } ],
	    items : [ {
		border : false,
		html : '<b>Kein Refrain ausgewählt / zugewiesen</b><br>' + "Klicke auf 'Weiter', um einen zu wählen.",
		refrainId : null
	    } ]
	});

	this.callParent();

	this.addCards();
    },

    addCards : function() {
	var store = this.songPanel.refrainStore;

	store.each(function(record, index, allRecords) {
	    var panel = Ext.create("Ext.panel.Panel", {
		border : false,
		html : record.get("Refrain"),
		refrainId : record.get("id")
	    });
	    this.add(panel);
	}, this);

	this.selectRefrainById(this.selectedRefrainId);
    },

    navigate : function(direction) {
	var layout = this.getLayout();
	layout[direction]();
	var activeItem = layout.getActiveItem();
	this.selectedRefrainId = activeItem.refrainId;
	this.fireEvent("selectionChanged", this.selectedRefrainId);
	this.enableDisableNavButtons();
    },

    /**
     * Enables and disables the next and previous button according to the
     * current position.
     */
    enableDisableNavButtons : function() {
	var layout = this.getLayout();
	this.down("#move-prev").setDisabled(!layout.getPrev());
	this.down("#move-next").setDisabled(!layout.getNext() || this.items.length < 2);
    },

    /**
     * Shows the refrain card based on the given id.
     * 
     * Note: The event selectionChanged will not be fired!
     * 
     * @param {int}
     *                refId
     */
    selectRefrainById : function(refId) {
	Ext.each(this.items.items, function(item, index, allItems) {
	    if (item.refrainId == refId) {
		this.getLayout().setActiveItem(item);
		return false;
	    }
	}, this);

	this.enableDisableNavButtons();
    }
});