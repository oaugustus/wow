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
Wow.Auth = Ext.extend(Ext.util.Observable,{
    failureAuthenticateMsg: 'Usu&aacute;rio ou senha inv&aacute;lidos!',
    emptyFormMsg: 'Os campos em destaque s&atilde;o obrigat&oacute;rios!',
    constructor : function(config){
        Wow.Auth.superclass.constructor.call(this);

        this.authAction = Ext.app.User;
        this.addEvents(
            'authenticate',
            'failureauthenticate'
        );
        
        this.authWin = new Wow.cfg.authWinClass({module: this});
    },

    initialize : function(){        
        this.authAction.checkSession(null,function(session){
            if (session){
                this.authWin.close();
                this.fireEvent('authenticate',this,session);
            }
            else{
                this.authWin.show();
            }
                
        },this);
    },
    
    failure : function(){
        Wow.Msg.flash({
            type: 'error',
            msg: this.failureAuthenticateMsg
        });
    },

    clear : function(){
        this.authWin.form.getForm().reset();
    },

    requestLogon : function(){
        var f = this.authWin.form.getForm();
        if (f.isValid()){
            this.authWin.accessButton.plugins[0].show();

            this.authAction.requestLogon({data: f.getValues()},function(session){
                this.authWin.accessButton.plugins[0].hide();
                if (session){
                    this.authWin.close();
                    this.fireEvent('authenticate',this,session);                    
                }else{
                    this.failure();
                }
            },this);
        }else{
            Wow.Msg.flash({
                type: 'error',
                msg: this.emptyFormMsg
            })
        }
    }
});