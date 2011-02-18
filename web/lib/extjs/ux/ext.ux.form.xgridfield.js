// vim: ts=4:sw=4:nu:fdc=2:nospell
/**
 * Ext.ux.form.GridField - an EditorGridPanel tha works as a form field
 *
 * @author  Otávio Augusto Rodrigues Fernandes
 * @date    8. July 2010
 *
 *
 * @license Ext.ux.form.GridField is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * License details: http://www.gnu.org/licenses/lgpl.html
 */

Ext.ns('Ext.ux.form');
/**
  * @class Ext.ux.form.GridField
  * @extends Ext.grid.GridPanel
  */
 Ext.ux.form.XGridField = Ext.extend(Ext.grid.GridPanel,{
     saveButtonText: '<b>Confirmar</b>',
     cancelButtonText: 'Cancelar',
     addButtonText: 'Novo',
     addButtonCls: 'wow-create',
     deleteButtonText: 'Excluir',
     deleteButtonCls: 'wow-delete',
     waitingDeleteText: 'Excluindo...',
     maskDisabled: false,
     /**
      * Inicializa o componente
      */
     initComponent : function(){
        // Cria o componente editor
        this.editor = new Ext.ux.grid.RowEditor({
            saveText: this.saveButtonText,
            cancelText: this.cancelButtonText
        });

        var defaults = {
            plugins:[this.editor],
            maskDisabled: false,
            tbar:[
                {
                    text: this.addButtonText,
                    iconCls: this.addButtonCls,
                    handler: this.addRecord,
                    scope: this,
                    ref: '../btnAdd'
                },'-',
                {
                    text: this.deleteButtonText,
                    iconCls: this.deleteButtonCls,
                    handler: this.deleteRecord,
                    scope: this,
                    ref: '../btnDelete',
                    plugins:[new Wow.Mask({waitingText: 'Excluindo...'})]
                }
            ]
        }

        Ext.apply(this, defaults);
        
        // chama o método init da superclasse
        Ext.ux.form.XGridField.superclass.initComponent.apply(this, arguments);

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
       this.store.on('load',this.setValue,this);
     },

     setValue : function(store){
        var recordsToSave = new Array();

        var i = 0;

        //Percorre a lista de registros modificados pegando seus dados
        store.each(function(rec){
            recordsToSave[i]= rec.data;
            i++;
        },this);

        //Codifica os registros modificados em uma string para envio ao backend
        var encoded = Ext.encode(recordsToSave);

        this.hiddenField.setValue(encoded);         
     },

     /**
      * Adiciona um novo registro
      */
     addRecord : function(){
        this.editor.doLayout();
        var Record = this.getStore().recordType;

        var r = new Record();

        this.editor.stopEditing();
        this.store.insert(this.store.getCount(),r);
        this.getView().refresh();
        this.getSelectionModel().selectRow(0);
        this.editor.startEditing(this.store.getCount()-1);
     },

     /**
      * Deleta um registro
      */
     deleteRecord : function(btn){
        Ext.MessageBox.confirm('Confirme', 'Deseja realmente excluir este registro?', function(b){
            if (b == 'yes'){
                var button = btn;
                this.editor.stopEditing();
                var s = this.getSelectionModel().getSelections();
                var save = false;
                btn.plugins[0].show();
                for(var i = 0, r; r = s[i]; i++){
                    if (r.id > 0){
                        save = true;
                    }

                    this.store.remove(r);

                }
                this.store.on('write',function(store, action){
                    if (action == 'destroy'){
                        button.plugins[0].hide();
                        // Exibe mensagem de confirmação
                        Wow.MessageBox.flash({
                           type: 'success',
                           msg: 'Registro excluído com êxito!'
                        });
                        this.setValue(store);
                   }
                },this);

                if (save){
                    this.store.save();
                }else{
                    button.plugins[0].hide();
                }
                

            }
        },this);
         
     }
 });

 Ext.reg('xgridfield',Ext.ux.form.XGridField);


