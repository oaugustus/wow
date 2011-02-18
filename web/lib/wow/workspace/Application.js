/*!
 * Web Office Workspace - Wow
 * Copyright(c) 2010.
 * @author Otávio Fernandes <oaugustus>
 */
Ext.ns('Wow');
/**
 * @class Wow.Application
 * @extends Ext.util.Observable
 * The application class
 * @singleton
 */
Wow.Application = Ext.apply(new Ext.util.Observable(),{
    run : function(){
        this.auth = new Wow.cfg.authClass();
        this.auth.on('authenticate',this.initialize,this);
        this.auth.initialize();
    },

    initialize : function(auth, session){
        Ext.app.Session = session;
        this.desktop = new Wow.cfg.desktopClass({session: session});
    }
});