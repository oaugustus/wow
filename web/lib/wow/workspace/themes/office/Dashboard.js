/*!
 * Web Office Workspace - Wow
 * Copyright(c) 2010.
 * @author Otávio Fernandes <oaugustus>
 */
Ext.ns('Wow.office');
/**
 * @class Wow.office.Dashboard
 * @extends Ext.Panel
 * Dashboard of Office theme
 */
Wow.office.Dashboard = Ext.extend(Ext.Panel,{
   border: false,
   layout: 'card',
   layoutConfig: {
     deferredRender: true
   },
   layoutOnCardChange: true,
   initComponent : function(){
       this.tbar = new Wow.office.Menu({modules: this.desktop.modules})
       Wow.office.Dashboard.superclass.initComponent.apply(this, arguments);
   },

   /**
    * @private
    */
   afterRender : function(){       
       this.initModules();

       Wow.office.Dashboard.superclass.afterRender.apply(this, arguments);
   },

   /**
    * @private
    */
   initModules : function(){
       var apps = this.desktop.modules, m, packs, modules;

       
       for (var i = 0; i < apps.length; i++){
           packs = apps[i].items;
           for (var j = 0; j < packs.length; j++){
               modules = packs[j].items;
               
               for (var k = 0; k < modules.length; k++){
                   m = {
                       xtype: modules[k].mtype,
                       id: modules[k].mId + '-module'
                   }
                   this.add(m);
               }
           }
       }

   },

   activate : function(id){
       this.layout.setActiveItem(id);
   }
});

Ext.reg('officedashboard',Wow.office.Dashboard);


