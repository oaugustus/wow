// vim: ts=4:sw=4:nu:fdc=2:nospell
/**
 * Ext.ux.form.XCheckGrid - an EditorGridPanel tha works as a form check field
 *
 * @author  Otávio Augusto Rodrigues Fernandes
 * @date    8. July 2010
 *
 *
 * @license Ext.ux.form.XCheckGrid is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * License details: http://www.gnu.org/licenses/lgpl.html
 */

Ext.ns('Ext.ux.form');
/**
  * @class Ext.ux.form.XCheckGrid
  * @extends Ext.grid.GridPanel
  */
 Ext.ux.form.XCheckGrid = Ext.extend(Ext.grid.GridPanel,{
     maskDisabled: false,
     /**
      * Inicializa o componente
      */
     initComponent : function(){
        var sm = new Ext.grid.CheckboxSelectionModel({
            listeners:{
                'selectionchange' : this.setValue,
                scope: this
            }
        });
        
        var defaults = {
            sm: sm,
            maskDisabled: false
        }

        Ext.apply(this, defaults);

        var cm = [sm].concat(this.columns);

        this.columns = cm;

        // chama o método init da superclasse
        Ext.ux.form.XCheckGrid.superclass.initComponent.apply(this, arguments);

        // cria o campo hidden
        this.hiddenField = new Ext.form.Hidden({
            name: this.name
        });

        // adiciona o campo hidden ao formulário
        this.refForm.add(this.hiddenField);

        this.registerEvents();
     },

     registerEvents : function(){
       this.store.on('datachanged',this.setValue,this);
       this.store.on('update',this.setValue,this);
       this.store.on('load',this.setChecked,this);
     },

     setChecked : function(store){
         store.each(function(rec){
            if (rec.data.selected >= 1){
                this.getSelectionModel().selectRecords([rec],true);
            }
         },this);
         this.setValue(this.getSelectionModel());
     },
     
     setValue : function(sm){        
        if (sm.xtype != 'groupingstore'){
            var recs = sm.getSelections();

            var recordsToSave = new Array();
            var field = this.fieldId;
            for (i = 0; i < recs.length; i ++){
                recordsToSave[i] = {};
                recordsToSave[i][field] = recs[i].data.id;
            }

            //Codifica os registros modificados em uma string para envio ao backend
            var encoded = Ext.encode(recordsToSave);

            this.hiddenField.setValue(encoded);
        }
     }

 });

Ext.reg('xcheckgrid',Ext.ux.form.XCheckGrid);


