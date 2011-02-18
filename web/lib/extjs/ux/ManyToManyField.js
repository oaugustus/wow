// vim: ts=4:sw=4:nu:fdc=2:nospell
/**
 * Ext.ux.grid.ManyToManyField - an plugin tha works as a form check field
 *
 * @author  Otávio Augusto Rodrigues Fernandes
 * @date    8. July 2010
 *
 *
 * @license Ext.ux.grid.ManytoManyField is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * License details: http://www.gnu.org/licenses/lgpl.html
 */

Ext.ns('Ext.ux.grid');
/**
  * @class Ext.ux.grid.ManyToManyField
  * @extends Ext.Container
  */
 Ext.ux.grid.ManyToManyField = Ext.extend(Ext.Container,{
     selectionField: 'selected',
     reload: true,
     loadingMsg: 'Carregando...',
     
     /**
      * Inicializa o componente
      */
     init : function(grid){

        this.grid = grid;
        
        var sm = new Ext.grid.CheckboxSelectionModel({
            grid: this.grid,
            listeners:{
                'selectionchange' : this.setValue,
                scope: this
            }
        });
        
        this.grid.selModel = sm;
        var defaultParams = {};
        defaultParams[this.foreign] = -1;
        
        this.grid.store.baseParams =  defaultParams;
        
        var c = [sm].concat(this.grid.colModel.config);        
        var cm = new Ext.grid.ColumnModel(c);

        this.grid.reconfigure(this.grid.store, cm);
        

        this.grid.onAdded = this.grid.onAdded.createSequence(this.onAdded, this);
        
     },

     onAdded : function(){
        this.refForm = this.grid.refOwner;

        // cria o campo hidden
        this.hiddenField = new Ext.form.Hidden({
            name: this.name,
            value: '[]'
        });

        // adiciona o campo hidden ao formulário
        this.refForm.add(this.hiddenField);

        this.registerEvents();
         
     },

     /**
      * Registra os eventos para grid do plugin
      */
     registerEvents : function(){
       //this.grid.store.on('datachanged',this.setValue,this);
       this.grid.store.on('update',this.setValue,this);
       this.grid.store.on('load',this.setChecked,this);
       this.refForm.getForm().on('reset',this.reset, this);
       this.refForm.getForm().on('setvalues',this.loadData, this);
       this.refForm.ownerCt.on('create',function(){
           this.reload = false;
           this.grid.store.load();
       },this);

     },

     /**
      * Load the grid datastore
      */
     loadData : function(values){
        // load the grid values
        if (values['id']){
            var params = {};
            params[this.foreign] = values['id'];

            this.masterID = values['id'];

            var mask = undefined;
            var rendered = this.grid.rendered;

            if (rendered){
                mask = new Ext.LoadMask(this.grid.getEl(),{msg: this.loadingMsg, removeMask: true});
                mask.show();
            }

            this.grid.store.load({
                params: params,
                callback: function(){
                    if (rendered)
                        mask.hide();
                }
            });
        }

     },

     // resets the fied and remove all grid records
     reset : function(){
       this.hiddenField.reset();

       this.grid.getSelectionModel().clearSelections();
       
     },

     /**
      * Seta o valor do campo hidden
      */
     setValue : function(sm){        
        if (sm.xtype != 'groupingstore'){
            var recs = sm.getSelections();

            var recordsToSave = new Array();
            var field = this.local;
            for (i = 0; i < recs.length; i ++){
                recordsToSave[i] = {};
                recordsToSave[i][field] = recs[i].data.id;
            }
            
            //Codifica os registros modificados em uma string para envio ao backend
            var encoded = Ext.encode(recordsToSave);

            this.hiddenField.setValue(encoded);
        }
     },

     /**
      * Seta as opções marcadas
      */
     setChecked : function(store){
         var s = store;
         if (this.grid.rendered){
             store.each(function(rec){
                if (rec.data[this.selectionField] >= 1){
                    this.grid.getSelectionModel().selectRecords([rec],true);
                }
             },this);
             this.setValue(this.grid.getSelectionModel());
         }else if (this.reload){
             this.grid.on('render',function(){                 
                mask = new Ext.LoadMask(this.grid.getEl(),{msg: this.loadingMsg, removeMask: true});
                mask.show();

                var params = {};
                params[this.foreign] = this.masterID ? this.masterID : -1;
                
                this.grid.store.load({
                    params:params,
                    callback: function(){
                        mask.hide();                        
                    }
                });
             },this);
         }
     }


 });

Ext.reg('manytomanyfield',Ext.ux.grid.ManyToManyField);


