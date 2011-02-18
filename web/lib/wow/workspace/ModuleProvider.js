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
               title: 'Cadastros',
               items:[
                   {
                      xtype: 'buttongroup',
                      title: 'Agenda',
                      items:[
                           {
                               text: 'Empresas',
                               mtype: 'cidade-module',
                               iconCls: 'cidade',
                               mId: 'cidade'
                           },
                           {
                               text: 'Pessoas',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           },
                           {
                               text: 'Feriados',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           }

                      ]
                   },
                   {
                      xtype: 'buttongroup',
                      title: 'Equipamento',
                      items:[
                           {
                               text: 'Equipamentos',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           },
                           {
                               text: 'Instala&ccedil;&atilde;o f&iacute;sica',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           },
                           {
                               text: 'Categorias',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           },
                           {
                               text: 'Modelos',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           }
                      ]
                   },
                   {
                      xtype: 'buttongroup',
                      title: 'Estoque',
                      items:[
                           {
                               text: 'Peças',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               scale: 'large',
                               mId: 'estado'
                           },
                           {
                               text: 'Estoque',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           }
                          
                      ]
                   },
                   {
                      xtype: 'buttongroup',
                      title: 'Preventiva',
                      items:[
                           {
                               text: 'Grupo',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               scale: 'large',
                               mId: 'estado'
                           },
                           {
                               text: '&nbsp;&nbsp;Item&nbsp;&nbsp;',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           },
                           {
                               text: 'Rotina',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           }

                      ]
                   },
                   {
                      xtype: 'buttongroup',
                      title: 'Controle de gases',
                      items:[
                           {
                               text: 'Gases medicinais',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               scale: 'large',
                               mId: 'estado'
                           },
                           {
                               text: 'Tanques e Cilindros',
                               mtype: 'estado-module',
                               iconCls: 'estado',
                               mId: 'estado'
                           }

                      ]
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


