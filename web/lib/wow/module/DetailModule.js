/*!
 * Web Office - Workspace
 * @author Otávio Augusto R. Fernandes - oaugustus
 * Copyright(c) 2010 Net On
 */

/**
 * @class Wow.DetailModule
 * @extends Wow.Module
 * <p>Base Module. </p>
 * @constructor
 * @param {Object} config The config object
 * @xtype w-detailmodule
 */
Wow.DetailModule = Ext.extend(Wow.Module,{
   /**
    * Apply basic configurations
    */
   initComponent : function(){
       Ext.apply(this,{
           layout: 'border'
       });

       this.addEvents(
          'refresh'
       );

      Wow.DetailModule.superclass.initComponent.apply(this, arguments);
   },

   // private
   onRender : function(){
       Ext.apply(this.dataListConfig,{
          region: 'center'
       });

       Wow.DetailModule.superclass.onRender.apply(this, arguments);
   },

   // private
   afterRender : function(){
       Wow.DetailModule.superclass.afterRender.apply(this, arguments);
       
       this.dataList.on('itemclick',function(){
          this.fireEvent('refresh',this);
       },this);
   }
});


