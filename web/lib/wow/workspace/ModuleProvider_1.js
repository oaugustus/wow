/*!
 * Web Office Workspace - Wow
 * Copyright(c) 2010.
 * @author Otávio Fernandes <oaugustus>
 */
Ext.ns('Wow.workspace');
/**
 * @class Wow.workspace.ModuleProvider
 * @extends Ext.util.Observable
 * Manager of Application UI Modules
 * @constructor
 * @param 
 */
Wow.workspace.ModuleProvider = function(){
    this.groups = new Ext.util.MixedCollection();
    
    Wow.workspace.ModuleProvider.superclass.constructor.call(this);
};

Ext.extend(Wow.workspace.ModuleProvider, Ext.util.Observable, {
   getModules : function(){
       return [
           {
               title: 'Grupo 1',
               items:[
                   {
                       title: 'Cidade',
                       mtype: 'cidade-module',
                       iconCls: 'cidade',
                       mId: 'cidade'
                   },{xtype: 'tbseparator'},
                   {
                       title: 'Estado',
                       mtype: 'estado-module',
                       iconCls: 'estado',
                       mId: 'estado'
                   }

               ]
           }/*,
           {
               title: 'Grupo 2',
               xtype: 'officemenugroup',
               items:[
                   {
                       title: 'Estado',
                       mtype: 'cidade-module',
                       iconCls: 'estado'
                   }
               ]
           }*/
       ]
   }

    
});


