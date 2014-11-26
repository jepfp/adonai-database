/**
 * Writer which before sending the data to the server tries to collect all
 * songbook associations for a song.
 */

Ext.namespace('Songserver');

Ext.define('Songserver.writer.SongWriter', {
	extend : 'Ext.data.writer.Json',
	requires : [ 'Ext.data.writer.Json' ],
	alias : 'writer.songWriter',

	getRecordData : function(record){
		//console.log("Datensatz wird gespeichert.");
		var modifiedRecord = this.callParent([ record ]);

		// now add the data from the associated songbooks
		var songbooks = new Object();
		record.songbooks().data.each(function(it){
			songbooks[it.data.id] = it.data;
		});
		modifiedRecord["songbooks"] = songbooks;
		
		return modifiedRecord;
	}
});