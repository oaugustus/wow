/*!
 * Web Office - Workspace
 * @author Otávio Augusto R. Fernandes - oaugustus
 * Copyright(c) 2010 Net On
 */

/**
 * @class Wow.Module
 * @extends Ext.Panel
 * <p>Base Module. </p>
 * @constructor
 * @param {Object} config The config object
 * @xtype w-module
 */
Wow.Module = Ext.extend(Ext.Panel,{
    /**
     * @cfg {String} actionsAlign
     * <p>The align to apply into module actions buttons on the tbar (defaults to left). Accept 'left', 'right', 'center'.</p>
     */
     actionsAlign : 'left',
    /**
     * @cfg {String} rootProperty
     * <p>The root property for module store (defaults to 'results').</p>
     */
     rootProperty : 'records',
    /**
     * @cfg {String} successProperty
     * <p>The success property for module store (defaults to 'success').</p>
     */
     successProperty : 'success',
    /**
     * @cfg {String} messageProperty
     * <p>The message property for module store (defaults to 'message').</p>
     */
     messageProperty : 'message',
    /**
     * @cfg {String} idProperty
     * <p>The id property for module store (defaults to 'id').</p>
     */
     idProperty : 'id',
    /**
     * @cfg {String} totalProperty
     * <p>The total property for module store (defaults to 'total').</p>
     */
     totalProperty : 'total',
    /**
     * @cfg {Ext.direct.Action/Object} directAction
     * <p>The Ext Direct action oject.</p>
     */
    /**
     * @cfg {Array[String/Object]} fields
     * <p>The fields avaiable in module (shared to form and list).</p>
     */
    /**
     * @cfg {String} saveActionText
     * <p>The save action text. (defaults to <code>Salvar</code>).</p>
     */
    saveActionText: 'Salvar (F12)',
    /**
     * @cfg {String} saveActionIconCls
     * <p>The save action icon class. (defaults to <code>wow-save</code>).</p>
     */
    saveActionIconCls: 'wow-save',

    /**
     * @cfg {String} createActionText
     * <p>The create action text. (defaults to <code>Novo</code>).</p>
     */
    createActionText: 'Novo',
    /**
     * @cfg {String} createActionIconCls
     * <p>The save action icon class. (defaults to <code>wow-create</code>).</p>
     */
    createActionIconCls: 'wow-create',
    /**
     * @cfg {String} confirmRemoveTitle
     * <p>The confirm remove record message title.</p>
     */
    confirmRemoveTitle: 'Confirme',
    /**
     * @cfg {String} confirmRemoveMsg
     * <p>The confirm remove record message.</p>
     */
    confirmRemoveMsg: 'Deseja realmente excluir este registro?',
    /**
     * @cfg {String} editActionText
     * <p>The edit action text. (defaults to <code>Editar</code>).</p>
     */
    editActionText: 'Editar',
    /**
     * @cfg {String} editActionIconCls
     * <p>The edit action icon class. (defaults to <code>wow-edit</code>).</p>
     */
    editActionIconCls: 'wow-edit',

    /**
     * @cfg {String} removeActionText
     * <p>The remove action text. (defaults to <code>Deletar</code>).</p>
     */
    removeActionText: 'Excluir',
    /**
     * @cfg {String} removeActionIconCls
     * <p>The remove action icon class. (defaults to <code>wow-remove</code>).</p>
     */
    removeActionIconCls: 'wow-delete',
    /**
     * @cfg {String} waitingRemoveText
     * <p>The waiting remove record message.</p>
     */
    waitingRemoveText: 'Excluindo...',
    /**
     * @cfg {String} waitingSaveText
     * <p>The waiting save record message.</p>
     */
    waitingSaveText: 'Salvando...',
    /**
     * @cfg {String} updatedRecordMsg
     * <p>The updated record message.</p>
     */
    updatedRecordMsg: 'Registros atualizados com &ecirc;xito!',

    /**
     * @cfg {String} storeType
     * <p>The datastore type.</p>
     */
    storeType: 'jsonstore',

    border: false,

   layout: 'fit',

    //private
    initComponent : function(){

        if (!this.apiCfg)
            this.apiCfg = {};
        
        /*var directIndex = this.directAction.index,
            directRemove = this.directAction.remove,
            directUpdate = this.directAction.saveBatch;*/

        Ext.applyIf(this.apiCfg, {
         read: this.directAction.index,
         destroy: this.directAction.remove,
         update: this.directAction.saveBatch
        });


        //create the module datastore
        this.createStore();

        //apply editor property into dataList Columns, if exists
        this.applyEditors();
        
        //static configs
        var dataList = {
            xtype: 'w-datalist',
            id: this.id + '-dl',
            store: this.store
        };
        
        //apply static configs into datalist
        Ext.apply(this.dataListConfig, dataList);

        //apply static configs
        Ext.apply(this, {
           iconCls: this.smallIconCls
        });

        this.addEvents(
            'create',
            'update',
            'read',
            'destroy'
        );
        
        Wow.Module.superclass.initComponent.call(this);        

        this.on('destroy',this.afterRemove, this);
        this.on('update', this.afterUpdate, this);
    },

    // private
    onRender : function(){
        this.add(this.dataListConfig);

        Wow.Module.superclass.onRender.apply(this, arguments);
    },

    // private
    afterRender : function(){
        Wow.Module.superclass.afterRender.apply(this, arguments);

        //create the data form for this module
        this.createForm();

        this.getProperties();

        var mask = new Ext.LoadMask(this.el);
        mask.show();
        this.store.load({callback: function(){
            mask.hide();
        }});

        this.on('show', function(){
            this.store.load();
        }, this);
    },

    /*
    onAdded : function(){
        Wow.Module.superclass.onAdded.apply(this, arguments);

        if (!this.hasActions){
            if (this.actionsAlign == 'right'){
                this.dataList.tbar.add(['->'].concat(this.getActions()));
            }
            else
            if (this.actionsAlign == 'left'){
                this.dataList.tbar.insertButton(0,this.getActions().concat(['','']));

            }
            else{
                this.dataList.tbar.add(['-'].concat(this.getActions()));
            }
            this.hasActions = true;

            this.dataList.tbar.doLayout();
        }
    },*/

    //private | apply field editors to dataList columns
    applyEditors: function(){

        for (k = 0; k < this.dataListConfig.columns.length; k++){
            col = this.dataListConfig.columns[k];
            for (i = 0; i < this.fields.length; i++){
                if (this.fields[i].name == col.dataIndex){
                    if (this.fields[i].editor){
                        if (!col.editor)
                            col.editor = this.fields[i].editor;
                    }                        
                    break;
                }
            }

        }

        
    },

    //private
    createStore : function(){
        var storeClass = '';

        if (!this.storeConfig){
            this.storeConfig = {xtype: 'jsonstore'};
        }

        // define a classe do datastore
        switch(this.storeConfig.xtype){
            case 'groupingstore':
                this.createGroupingStore();
            break;
            default:
               this.createJsonStore();
            break;
        }

    },

    /**
     * Cria componente do tipoo grouping stor
     */
    createGroupingStore : function(){
        var cfg = {
          proxy: new Ext.data.DirectProxy({
            api:this.apiCfg
          }),

          writer: new Ext.data.JsonWriter({
              encode: false,
              encodeDelete: true,
              listfull: true,
              writeAllFields: this.storeConfig.writeAllFields ? this.storeConfig.writeAllFields : false
          }),
          reader: new Ext.data.JsonReader({
              root: this.rootProperty,
              successProperty: this.successProperty,
              messageProperty: this.messageProperty,
              idProperty: this.storeConfig.idProperty  ? this.storeConfig.idProperty : this.idProperty,
              fields: this.fields,
              totalProperty: this.totalProperty
          }),
          autoLoad: false,
          remoteSort: true,
          baseParams:{
            start: 0,
            limit: 200
          },
          autoSave: false,
          listeners:{
              'write' : function(store, action, data, response, rs, options){
                  this.fireEvent(action, data, response, rs, options);
              },
              'remove' : function(store, rs){
                 store.save();
              },
              scope: this
          }

        };
                
        Ext.apply(cfg, this.storeConfig);

        this.store = new Ext.data.GroupingStore(cfg);
    },

    /**
     * Cria componente do tipo jsonstore
     */
    createJsonStore : function(){
        var cfg = {
          proxy: new Ext.data.DirectProxy({
             api: this.apiCfg
          }),
          writer: new Ext.data.JsonWriter({
              encode: false,
              encodeDelete: true,
              listfull: true,
              writeAllFields: this.storeConfig.writeAllFields ? this.storeConfig.writeAllFields : false
          }),
          root: this.rootProperty,
          successProperty: this.successProperty,
          messageProperty: this.messageProperty,
          idProperty: this.idProperty,
          fields: this.fields,
          totalProperty: this.totalProperty,
          autoLoad: false,
          remoteSort: true,
          baseParams:{
            start: 0,
            limit: 200
          },
          autoSave: false,
          listeners:{
              'write' : function(store, action, data, response, rs, options){
                  this.fireEvent(action, data, response, rs, options);
              },
              'remove' : function(store, rs){
                 store.save();
              },
              scope: this
          }

        };
        Ext.apply(cfg, this.storeConfig);

        this.store = new Ext.data.JsonStore(cfg);
    },

    //private
    createForm : function(){
        var formConfig = {
            xtype: 'w-dataform',
            module: this
        }

        Ext.apply(formConfig, this.dataFormConfig);
        
        this.dataForm = new Wow.form.DataForm(formConfig);

        if (this.dataFormConfig.editConfig){
            if (this.dataFormConfig.editConfig.ftype){
                var edtForm = {xtype: 'w-dataform', module: this};
                Ext.apply(edtForm,this.dataFormConfig.editConfig)
                
                this.dataFormEdit = new Wow.form.DataForm(edtForm);
            }else{
                this.dataFormEdit = this.dataForm;
            }                
        }
        
    },
    
    //private
    getProperties : function(){
        this.dataList = Ext.getCmp(this.id + '-dl')
    },
    
    //private
    getActions : function(){
        var actions = [];
        var mName = this.basePermission ? this.basePermission : this.id.split('-').shift();
        Ext.each(this.actions, function(item){
            var pre = '';
            var pos = '', added = false;
            if (item.primary){
                pre = '<b>';
                pos = '</b>';
            }

            switch(item.action){
                case 'save':
                    if (Ext.app.Session.privileges[mName+"-save"]){
                        added = true;
                        actions.push({
                           text: pre + this.saveActionText + pos,
                           iconCls: this.saveActionIconCls,
                           handler: this.executeUpdate,
                           plugins: [new Wow.Mask({store: this.store, action: 'update', waitingText: this.waitingSaveText})],
                           scope: this
                        });
                    }
                break;
                case 'create':
                    if (Ext.app.Session.privileges[mName+'-create']){
                        added = true;
                        actions.push({
                           text: pre + this.createActionText + pos,
                           iconCls: this.createActionIconCls,
                           handler: this.executeCreate,
                           scope: this
                        });
                    }
                break;
                case 'edit':
                    if (Ext.app.Session.privileges[mName+'-edit']){
                        added = true;
                        actions.push({
                           text: pre + this.editActionText + pos,
                           iconCls: this.editActionIconCls,
                           handler: this.executeEdit,
                           scope: this
                        });
                    }
                break;
                case 'remove':
                    if (Ext.app.Session.privileges[mName+'-remove']){
                        added = true;
                        actions.push({
                           text: pre + this.removeActionText + pos,
                           iconCls: this.removeActionIconCls,
                           handler: this.executeRemove,
                           plugins: [new Wow.Mask({store: this.store, action: 'destroy', waitingText: this.waitingRemoveText})],
                           scope: this
                        });
                    }
                break;
                default:
                    if (item.permission){
                        added = true;
                        if (Ext.app.Session.privileges[item.permission]){
                            actions.push(item);
                        }
                    }else{
                        if (item.text){
                            added = true;
                            actions.push(item);
                        }
                    }
                    if (item.menu){
                        Ext.each(item.menu.items, this.applyPriv,this);
                    }


                break;
            }
            if (added)
                actions.push('-');
        },this);
        return actions;
    },

    // aplica permissões para os items do menu
    applyPriv : function(e){
        if (e.menu){
            Ext.each(e.menu.items, this.applyPriv, this);
            
            if (e.permission){
                if (!Ext.app.Session.privileges[e.permission]){
                    e.disabled = true;
                }                
            }
        }
    },
    
    /**
     * @private 
     */
    executeCreate : function(){
        this.dataForm.create();
    },
    
    //private
    executeUpdate : function(){
        this.onUpdate();
    },

    //private
    onUpdate : function(){
        this.store.save();
    },
    
    executeEdit : function(){
        this.dataFormEdit.edit(this.dataList.getSelected(true));
    },

    //private
    executeRemove : function(){
        this.onRemove(this.dataList.getSelections(true));
    },

    //private
    onRemove : function(record){
        if (record){
            if (Ext.MessageBox.confirm(this.confirmRemoveTitle, this.confirmRemoveMsg, function(btn){
                if (btn == 'yes'){
                    this.store.remove(record)
                }
            }, this));            
        }
    },

    //private
    afterRemove : function(d, r, rs, o){
        this.dataList.fireEvent('itemremoved',d, r, rs, o);
    },

    //private
    afterUpdate : function(){
       Wow.Msg.flash({
          type: 'success',
          msg: this.updatedRecordMsg
       });
    }

});
Ext.reg('w-module',Wow.Module);


