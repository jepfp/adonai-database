/**
 * Proxy which is adjusted to work with Symfony 1.4. Philipp Jenni
 */

Ext.namespace('Songserver');

Ext.define('Songserver.SymfonyProxy', {
			extend : 'Ext.data.proxy.Rest',
			requires : ['Ext.data.proxy.Rest', 'Songserver.writer.SongWriter'],
			alias : 'proxy.symfonyProxy',

			/**
			 * We need other URLs because of Symfony. This method which
			 * overrides the base method changes the URL according to our needs.
			 */
			buildUrl : function(request) {
				var url = this.getUrl(request);
				// console.log(request.operation);
				// console.log("Url b4: " + url);

				// if the url doesn't already end with a / we add one
				if (url.substr(-1) !== "/") {
					url += "/";
				}

				if (request.operation.action == "read") {
					if (request.operation.id == undefined) {
						/*
						 * If no id property was added to the operation it will
						 * be an index read operation. ==> return all the
						 * objects
						 */
						url += "index";
					} else if (request.operation.id == 0) {
						// If id == 0 we want to create a new
						// item based on a preapred object.
						// Note maybe this can cause side effects. If so
						// see earlier note here:
						// If id == 0, make sure, that the server
						// doesn't return just the first (1) tupel. Add an extra
						// slash.
						// This case appears when Ext JS tries to request
						// a referenced object that is null (e. g. a verse that
						// has no
						// refrain.)
						url += "show/new";
					} else {
						url += "show/id";
					}
				} else if (request.operation.action == "create") {
					url += "create";
				} else if (request.operation.action == "update") {
					url += "update/id";
				} else if (request.operation.action == "destroy") {
					url += "delete/id";
				}

				// console.log("Url afterwords: " + url);
				request.url = url;

				return this.callParent(arguments);
			}
		});