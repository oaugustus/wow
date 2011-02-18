/*!
 * Web Office Workspace - Wow
 * Copyright(c) 2010.
 * @author Otávio Fernandes <oaugustus>
 */
Ext.ns('Wow');
/**
 * @class Wow.Office
 * @extends Ext.Viewport
 * Office 2007 theme for Wow
 */
Wow.Office = Ext.extend(Ext.Viewport,{
    layout: 'border',

    initComponent : function(){
        this.moduleProvider = new Wow.workspace.ModuleProvider();
        this.modules = this.session.modules;
        
        this.items = [
            {
                xtype: 'toolbar',
                itemId: 'header',
                region: 'north',
                height: 45,
                enableOverflow: true,
                items:[
                    {
                        xtype: 'box',
                        autoEl: {tag: 'img', src: 'images/logo.png', width:32, style: 'cursor: arrow;margin-top:5px;'}
                    },'',
                    {
                        text: '<b>' + Ext.app.SYSTEM_NAME + '</b>',
                        handleMouseEvents: false,
                        style: 'font-size: 14px;margin-top: 5px;color: #333;',
                        scale: 'large',
                        xtype: 'tbtext'
                    },'->','-',
                    {
                        text: '<b> Seja bem vindo(a): ' + this.session.fullname + '</b>',
                        handleMouseEvents: false,
                        scale: 'medium',
                        iconCls: 'loggeduser-icon'
                    },
                    '-',
                    {
                        text: '<b>Sair&nbsp;</b>',
                        iconCls: 'exit-icon',
                        handler: this.requestLogout,
                        scope: this,
                        scale: 'medium'
                    },''
                ]
            },{
                region: 'center',
                itemId: 'dashboard',
                xtype: 'officedashboard',
                desktop: this
            }/*,{
                xtype: 'toolbar',
                region: 'south',
                itemId: 'footer',
                items:[
                    {
                        text: 'Rodapé'
                    }
                ]
            }*/
        ]
        Wow.Office.superclass.initComponent.apply(this, arguments);
    },    

    afterRender : function(){
        Wow.Office.superclass.afterRender.apply(this, arguments);
        this.getProperties();
        this.dashboard.layout.setActiveItem(0);
    },

    /**
     * Solicita confirmação de encerramento da sessão e processa a mesma
     * caso a confirmação seja fornecida.
     */
    requestLogout : function(){
      Ext.Msg.confirm('Logoff','Deseja realmente sair do sistema?',function(btn){
          if (btn == 'yes'){
              Ext.app.User.requestLogout({},function(r){
                 window.location.reload();
              });
          }
      },this);
    },

    /**
     * @private
     */
    getProperties : function(){
        this.header = this.getComponent('header');
        this.dashboard = this.getComponent('dashboard');
        this.footer = this.getComponent('footer');
    }
});

Ext.reg('officetheme', Wow.Office);



