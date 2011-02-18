// vim: ts=4:sw=4:nu:fdc=2:nospell
/**
 * Ext.ux.grid.1xNGrid - an EditorGridPanel tha works as a form field
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
 Ext.ux.grid.OnexNGrid = Ext.extend(Ext.Container,{
     saveButtonText: '<b>Confirmar</b>',
     cancelButtonText: 'Cancelar',
     addButtonText: 'Novo',
     addButtonCls: 'wow-create',
     deleteButtonText: 'Excluir',
     deleteButtonCls: 'wow-delete',
     waitingDeleteText: 'Excluindo...',
     loadingMsg: 'Carregando...',
     maskDisabled: false,
     /**
      * Inicializa o componente
      */
     initComponent : function(){
        // chama o método init da superclasse
        Ext.ux.grid.OnexNGrid.superclass.initComponent.apply(this, arguments);
     },

     init : function(grid){

        this.grid = grid;

        // redefine o modelo de seleção de linhas
        this.grid.selModel = new Ext.grid.RowSelectionModel();
        this.grid.onEditorKey = Ext.emptyFn;

        // Cria o componente editor
        this.editor = new Ext.ux.grid.RowEditor({
            saveText: this.saveButtonText,
            cancelText: this.cancelButtonText
        });

        // adiciona o plugin row editor ao grid
        this.grid.plugins.push(this.editor);

        // inicializa o plugin editor
        this.editor.init(this.grid);

        // do our processing after grid render and reconfigure
        this.grid.onRender = this.grid.onRender.createSequence(this.onRender, this);

        // cria o campo hidden
        this.hiddenField = new Ext.form.Hidden({
            name: this.name
        });

        // adiciona o campo hidden ao formulário
        this.refForm.add(this.hiddenField);

        this.registerEvents();
     },

     onRender : function(){
         var bar = this.grid.topToolbar ? this.grid.topToolbar : this.grid.bottomToolbar;
         
        // se a barra de topo não existir, cria essa barra
          bar.add([
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
         ]);

        //this.grid.tbar.doLayout();
        this.grid.doLayout();

     },

     registerEvents : function(){
       this.grid.store.on('datachanged',this.setValue,this);
       this.grid.store.on('update',this.setValue,this);
       this.grid.store.on('load',this.setValue,this);
       this.refForm.getForm().on('reset',this.reset, this);
       this.refForm.getForm().on('setvalues',this.loadData, this);
     },

     loadData : function(values){
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
                }
            });
        }
     },
     
     // resets the fied and remove all grid records
     reset : function(){
       this.hiddenField.reset();
       this.grid.store.removeAll();
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
        var Record = this.grid.getStore().recordType;

        var r = new Record();

        this.editor.stopEditing();
        this.grid.store.insert(this.grid.store.getCount(),r);
        this.grid.getView().refresh();
        try{
          this.grid.getSelectionModel().selectRow(0);
        }catch(e){}
        
        this.editor.startEditing(this.grid.store.getCount()-1);
     },

     /**
      * Deleta um registro
      */
     deleteRecord : function(btn){
         var s;
        Ext.MessageBox.confirm('Confirme', 'Deseja realmente excluir este registro?', function(b){
            if (b == 'yes'){
                var button = btn;
                this.editor.stopEditing();

                try{
                  s = this.grid.getSelectionModel().getSelections();
                }catch(e){
                    var index = this.grid.getSelectionModel().getSelectedCell();
                    var s = false;

                    if (index) {
                        s = this.grid.store.getAt(index[0]);
                    }

                }

                var save = false;
                btn.plugins[0].show();
                for(var i = 0, r; r = s[i]; i++){
                    if (r.id > 0){
                        save = true;
                    }

                    this.grid.store.remove(r);

                }
                this.grid.store.on('write',function(store, action){
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
                    this.grid.store.save();
                }else{
                    button.plugins[0].hide();
                }
                

            }
        },this);
         
     }
 });

Ext.preg('onexngrid', Ext.ux.grid.OnexNGrid);


