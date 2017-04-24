Ext.namespace('Songserver.view.auth');

Ext.define('Songserver.view.auth.LoginForm', {
    extend : 'Ext.form.Panel',
    requires : [ 'Ext.form.action.DirectSubmit' ],
    alias : 'widget.songserver-loginform',
    title : SCOTTY_CLIENT_CONFIGURATION.projectTitle,
    frame: true,
    width: 500,

    api : {
	submit : 'Authentication.login'
    },

    // Fields will be arranged vertically, stretched to full width
    defaults : {
	anchor : '100%'
    },

    layout : 'anchor',
    bodyPadding : 5,

    // The fields
    defaultType : 'textfield',
    items : [ {
	fieldLabel : 'E-Mail',
	name : 'email',
	allowBlank : false,
	vtype : 'email',
	listeners : {
	    afterrender : function(field) {
		//allow autocomplete so that previously entered e-mail addresses show up
		field.inputEl.set({
                    autocomplete: 'on'
                });
		field.focus();
	    }
	}
    }, {
	fieldLabel : 'Password',
	name : 'password',
	inputType : 'password',
	allowBlank : false
    } ],

    buttons : [ {
	text : 'Registrieren',
	handler : function() {
	    Songserver.AppContext.mainLayout.loadPanel("Songserver.view.auth.RegistrationForm");
	}
    }, {
	text : 'Anmelden',
	handler : function() {
	    this.up('form').doLogin();
	}
    } ],

    listeners : {
	afterRender : function(thisForm, options) {
	    this.keyNav = Ext.create('Ext.util.KeyNav', this.el, {
		enter : this.doLogin,
		scope : this
	    });
	}
    },

    doLogin : function() {
	Songserver.AppContext.viewport.setLoading(true);
	var form = this.getForm();
	form.submit({
	    success : function(form, action) {
		Songserver.AppContext.setLoggedIn(action.result);
		Songserver.AppContext.viewport.loadApplicationLayout();
	    },
	    failure : function(form, action) {
		Ext.Msg.alert('Fehler', 'Anmeldung fehlgeschlagen!');
		Songserver.AppContext.viewport.setLoading(false);
	    }
	});
    }
});