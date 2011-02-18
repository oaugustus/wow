/*!
 * Web Office Workspace - Wow
 * Copyright(c) 2010.
 * @author Otávio Fernandes <oaugustus>
 */
Ext.ns('Wow.office');
/**
 * @class Wow.office.Menu
 * @extends Ext.TabPanel
 * Office Theme Menu
 */
Wow.office.Menu = Ext.extend(Ext.TabPanel,{
   activeItem : 0,
   border: false,
   hideBorders: true,
   menuGroupClass: 'officemenugroup',
   
   initComponent : function(){
       this.setClass();
       this.items = this.modules;
       Wow.office.Menu.superclass.initComponent.apply(this, arguments);
   },

   /**
    * @private
    */
   setClass : function(){
       for (i = 0; i < this.modules.length; i++){
           this.modules[i].xtype = this.menuGroupClass;
       }
       
   },

   activate : function(id){
       this.ownerCt.activate(id);
   }

});