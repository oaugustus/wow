// vim: ts=4:sw=4:nu:fdc=2:nospell
/**
 * Ext.ux.grid.OneToManyField - plugin to add one to many relation field in a editorgrid
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

Ext.ns('Ext.ux.grid');
/**
  * @class Ext.ux.grid.GridField
  * @extends Ext.grid.GridPanel
  */
 Ext.ux.grid.OneToManyField = Ext.extend(Ext.Container,{
     addButtonText: 'Novo',
     addButtonCls: 'wow-create',
     deleteButtonText: 'Excluir',
     deleteButtonCls: 'wow-delete',
     waitingDeleteText: 'Excluindo...',
     loadingMsg: 'Carregando...',
     deleteTitle: 'Confirme',
     deleteMsg: 'Deseja realmente excluir este registro?',
     successDeleteMsg: 'Registro excluído com êxito!',
     maskDisabled: false,
     showButtons: true,

     init : function(grid){
        this.grid = grid;
        
        // do our processing after grid render and reconfigure
        this.grid.onRender = this.grid.onRender.createSequence(this.onRender, this);
        this.grid.onAdded = this.grid.onAdded.createSequence(this.onAdded, this);

        // cria o campo hidden
        this.hiddenField = new Ext.form.Hidden({
            name: this.name
        });
     },

     onRender : function(ct){

        if (this.showButtons){
             //var bar = new Ext.Toolbar();
             var bar = this.grid.topToolbar ? this.grid.topToolbar : this.grid.bottomToolbar;

             // se a barra de topo não existir, cria essa barra
             bar.add([
                    {
                        text: this.addButtonText,
                        iconCls: this.addButtonCls,
                        handler: this.addRecord,
                        scope: this,
                        ref: '../../btnAdd'
                    },'-',
                    {
                      text: this.deleteButtonText,
                      iconCls: this.deleteButtonCls,
                      handler: this.deleteRecord,
                      scope: this,
                      ref: '../../btnDelete',
                      plugins:[new Wow.Mask({waitingText: this.waitingDeleteText})]
                    }
             ]);
            
        }         
     },

     onAdded : function(){
        this.refForm = this.grid.refOwner;
        
        // adiciona o campo hidden ao formulário
        this.refForm.add(this.hiddenField);

        // registra os eventos para os componentes
        this.registerEvents();
     },

     registerEvents : function(){
       this.grid.store.on('datachanged',this.setValue,this);
       this.grid.store.on('update',this.setValue,this);
       this.grid.store.on('load',this.setValue,this);
       this.refForm.getForm().on('reset',this.reset, this);
       this.refForm.getForm().on('setvalues',this.loadData, this);
     },

     loadData : function(values){

        if (!this.grid.rendered) {
            this.grid.on('render', function(){
                this.loadData(values);
            }, this);
            return;
        }

        // load the grid values
        if (values['id']){
            var params = {};
            params[this.foreign] = values['id'];

            var mask = undefined;
            
            mask = new Ext.LoadMask(this.grid.getEl(),{msg: this.loadingMsg, removeMask: true});
            mask.show();
            
            this.grid.store.load({
                params: params,
                callback: function(){
                    mask.hide();
                    this.grid.store.commitChanges();
                },
                scope: this
            });
        }
     },
     
     // resets the fied and remove all grid records
     reset : function(){
       this.hiddenField.reset();
       this.grid.store.removeAll();
       this.grid.store.commitChanges();
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
        this.grid.store.commitChanges();
     },

     /**
      * Adiciona um novo registro
      */
     addRecord : function(){
        var Record = this.grid.getStore().recordType;

        var r = new Record();

        this.grid.stopEditing();
        this.grid.store.insert(this.grid.store.getCount(),r);        
        this.grid.startEditing(this.grid.store.getCount()-1,0);
     },

     /**
      * Deleta um registro
      */
     deleteRecord : function(btn){

        Ext.MessageBox.confirm(this.deleteTitle, this.deleteMsg, function(b){
            var r = false;
            if (b == 'yes'){
                var button = btn;


                this.grid.stopEditing();

                var index = this.grid.getSelectionModel().getSelectedCell();
                if (index) {
                    r = this.grid.store.getAt(index[0]);
                }

                var save = false;
                btn.plugins[0].show();

                if (r.id)
                    save = true;
                
                if (save){
                    this.grid.store.proxy.api.destroy({records:{id: r.id}},function(response){
                        this.grid.store.remove(r);
                        button.plugins[0].hide();
                        // Exibe mensagem de confirmação
                        Wow.MessageBox.flash({
                           type: 'success',
                           msg: this.successDeleteMsg
                        });
                        this.setValue(this.grid.store);
                        
                    },this);
                }else{
                    button.plugins[0].hide();
               }
                
            }
        },this);
         
     }
 });

Ext.preg('onetomanyfield', Ext.ux.grid.OneToManyField);


