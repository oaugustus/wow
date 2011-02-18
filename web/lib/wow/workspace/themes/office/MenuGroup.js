/*!
 * Web Office Workspace - Wow
 * Copyright(c) 2010.
 * @author Otávio Fernandes <oaugustus>
 */
Ext.ns('Wow.office.menu');
/**
 * @class Wow.office.menu.Group
 * @extends Ext.Container
 * Office Theme Menu Group
 */
Wow.office.menu.Group = Ext.extend(Ext.Panel,{
   border: false,
   height: 85,
   initComponent : function(){
       var m;
       this.modules = this.items;
       delete(this.items);

       for (i = 0; i < this.modules.length; i++){
            m = this.modules[i];
            
            if (!m.xtype){
                m.xtype = 'button';
                m.scale = 'large';
                m.text = this.modules[i].title;
                m.iconAlign = 'top';
                m.handler = this.activate;
                m.scope = this;
                m.enableToggle = true;
                m.toggleGroup = 'menu-' + this.id;

                if (i == 0){
                    m.pressed = true;
                    this.firstModule = m;
                }
                    
            }else
            if (m.xtype == 'buttongroup'){
                var me = m.items;

                for (j = 0; j < m.items.length; j++){
                    me = m.items[j];
                    if (!me.xtype){
                        me.xtype = 'button';
                        me.scale = 'large';
                        me.text = me.text;
                        me.iconAlign = 'top';
                        me.handler = this.activate;
                        me.scope = this;
                        me.enableToggle = true;
                        me.toggleGroup = 'menu-' + this.id;
                        if (i == 0 && j == 0 ){
                            me.pressed = true;
                            this.firstModule = me;
                        }
                    }
                }
            }
       }
       this.items = [
           {
               xtype: 'toolbar',
               items:this.modules,
               enableOverflow: true
           }
       ];
       Wow.office.menu.Group.superclass.initComponent.apply(this, arguments);

       this.on('show',this.showCurrent,this);
   },

   activate : function(sender){
       this.currentModule = sender;
       this.ownerCt.activate(this.currentModule.mId+'-module');
   },

   showCurrent : function(){
       if (this.currentModule != undefined){
           this.activate(this.currentModule);
       }else
           this.activate(this.firstModule);
   }
});

Ext.reg('officemenugroup',Wow.office.menu.Group);

