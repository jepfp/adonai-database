Ext.namespace('Songserver.view.auth');

Ext.define('Songserver.view.auth.RegistrationForm', {
    extend : 'Ext.form.Panel',
    requires : [ 'Ext.form.action.DirectSubmit' ],
    alias : 'widget.songserver-registrationform',
    title : 'Registrieren',
    frame : 'true',
    width : 500,

    api : {
	submit : 'ManageUser.register'
    },

    // Fields will be arranged vertically, stretched to full
    // width
    defaults : {
	anchor : '100%'
    },

    layout : 'anchor',
    bodyPadding : 5,

    // The fields
    fieldDefaults : {
	type : 'textfield',
	labelWidth : 200
    },
    defaultType : 'textfield',
    items : [ {
	xtype : 'component',
	style : 'margin-bottom:10px',
	html : 'Bitte registriere dich. Nach der Registration musst du zuerst manuell freigeschaltet werden (ca. 1 Tag).<br> Bei Fragen kontaktiere uns bitte via <a href="mailto:lieder@adoray.ch">lieder@adoray.ch</a>.'
    }, {
	fieldLabel : 'Vorname',
	name : 'firstname',
	allowBlank : false,
	listeners : {
	    afterrender : function(field) {
		field.focus();
	    }
	}
    }, {
	fieldLabel : 'Nachname',
	name : 'lastname',
	allowBlank : false
    }, {
	fieldLabel : 'Adoray',
	name : 'adoray',
	allowBlank : false
    }, {
	fieldLabel : 'E-Mail',
	name : 'email',
	allowBlank : false,
	vtype : 'email'
    }, {
	fieldLabel : 'Password',
	name : 'password',
	inputType : 'password',
	allowBlank : false
    }, {
	fieldLabel : 'Password wiederholen',
	name : 'passwordRepeat',
	inputType : 'password',
	allowBlank : false
    } ],

    buttons : [ {
	text : 'Abbrechen',
	handler : function() {
	    Songserver.AppContext.mainLayout.loadPanel("Songserver.view.auth.LoginForm");
	}
    }, {
	text : 'Registrierung abschliessen',
	formBind : true, // only enabled when form
	// valid
	disabled : true,
	handler : function() {
	    Songserver.AppContext.viewport.setLoading(true);
	    var form = this.up('form').getForm();
	    form.submit({
		success : function(form, action) {
		    Songserver.AppContext.viewport.setLoading(false);
		    Ext.Msg.alert('Registration abgeschlossen', 'Deine Registration wurde erfolgreich entgegengenommen. Du wirst per E-Mail informiert, sobald wir dich manuell freigeschaltet haben.');
		    Songserver.AppContext.mainLayout.loadPanel("Songserver.view.auth.LoginForm");
		},
		failure : function(form, action) {
		    // Wenn eine Exception
		    // geworfen wird, ist
		    // action.result leer`!
		    Ext.Msg.alert('Fehler', action.result.message);
		    Songserver.AppContext.viewport.setLoading(false);
		}
	    });
	}
    } ]
});