/*!
 * Web Office - Workspace
 * @author Otávio Augusto R. Fernandes - oaugustus
 * Copyright(c) 2010 Net On
 */

/**
 * @class Wow.list.DataList
 * @extends Ext.Container
 * <p>Encapsulates all useful functionalities related to grid, editorgrid and
 * dataview(data list) components to the Wow's module. </p>
 * @constructor
 * @param {Object} config The config object
 * @xtype w-datalist
 */
Wow.list.DataList = Ext.extend(Ext.Container, {
    /**
     * The DataList bottom bar
     * <p>Toolbar where is pagging(if it's enabled)</p>
     * @type Ext.Toolbar
     * @property bbar
     */
    /**
     * The DataList (grid, editorgrid or dataview) component
     * <p>Base list component</p>
     * @type Ext.grid.GridPanel/Ext.grid.EditorGridPanel/Ext.DataView
     * @property list
     */
    /**
     * The DataList filter plugin instance
     * <p>Filter plugin instance</p>
     * @type Wow.list.Filter
     * @property filter
     */
    /**
     * The DataList top bar
     * <p>Toolbar where is the module actions and filter(if it's enabled)</p>
     * @type Ext.Toolbar
     * @property tbar
     */
    /**
     * @cfg {String} adapter
     * <p>The adapter of data list. The acceptable xtype values are:</p>
     * the acceptable xtype values are:
     * <div class="mdetail-params"><ul>
     * <li><b><tt>'grid'</tt></b> : <b>Default</b>
     * <p>An {@link Ext.grid.GridPanel} component</p></li>
     * <li><b><tt>'editorgrid'</tt></b> :
     * <p>An {@link Ext.grid.EditorGridPanel} component</p></li>
     * <li><b><tt>'dataview'</tt></b> :
     * <p>An {@link Ext.DataView} component</p></li>
     * </ul></div>
     */
    adapter: 'grid',
    /**
     * @cfg {Object} adapterConfig
     * <p>Object configurartion of list component. (grid, editorgrid or dataview) config</p>
     */
    adapterConfig: {},
    /**
     * @cfg {Boolean} autoLoad
     * <p>Enable or disable the autload for datalist</p>
     * <code>true</code> to autoload the datalist,
     * <code>false</code> to not autoload list (defaults to <code>false</code>).
     */
    /**
     * @cfg {Array} columns
     * <p>An array of columns to display in data list.</p>
     */
    /**
     * @cfg {Boolean} enableFilter
     * <p>Enable or disable a filter to data list in toolbar</p>
     * <code>true</code> to show and enable a{@link Wow.list.Filter} in toolbar,
     * <code>false</code> to not display the filter toolbar (defaults to <code>true</code>).
     */
    enableFilter: true,    
    /**
     * @cfg {Boolean} enablePaging
     * <p>Enable or disable a paging toolbar to data list</p>
     * <code>true</code> to show and enable a {@link Ext.PagingToolbar},
     * <code>false</code> to not display the paging (defaults to <code>true</code>).
     */
    enablePaging: true,
    /**
     * @cfg {String} filterMode Use 'remote' for remote stores or 'local' for local stores. If mode is local
     * no data requests are sent to server the grid's store is filtered instead (defaults to 'remote')
     */
    filterMode: 'remote',
    /**
     * @cfg {String} idProperty
     * <p>Name of the property within a row object that contains a record identifier value. (defaults to <code>id</code>).</p>
     */
    idProperty: 'id',
    /**
     * @cfg {Object} layout
     * <p>The layout manager (defaults to <code>'fit'</code>)</p>
     */
    layout: 'fit',
    /**
     * @cfg {Number} noSelectedRecordMsg
     * <p>Message to display case none record is selected in the list</p>
     */
    noSelectedRecordMsg: 'Nenhum registro foi selecionado!',
    /**
     * @cfg {Number} noSelectedRecordException
     * <p>Type of flash message case none record is selected in the list</p>
     */
    noSelectedRecordException: 'warning',
    /**
     * @cfg {Number} noSelectedRecordMsg
     * <p>Message to display case none record is selected in the list</p>
     */
    recordRemovedMsg: 'O registro foi exclu&iacute;do com &ecirc;xito!',
    /**
     * @cfg {Number} pageSize
     * <p>Page size for pagination, if it is enabled</p>
     */
    pageSize: 200,
    /**
     * @cfg {String} root
     * <p>The name of the property which contains the Array of row objects (defaults to <code>'results'</code>).</p>
     */
    root: 'results',
    /**
     * @cfg {Boolean} singleSelect
     * <p>Allow to select only one item in data list</p>
     * <code>true</code> to allow only a single select,
     * <code>false</code> to allow multiple selections in the data list (defaults to <code>true</code>).
     */
    singleSelect: true,
    /**
     * @cfg {Ext.data.Store} store
     * <p>An {@link Ext.data.Store} to provide data to the data list</p>
     */
    /**
     * @cfg {String} tpl
     * <p>The template to apply at list if it's a dataview.</p>
     */
    //tpl: null,
    /**
     * @cfg {String} totalProperty
     * <p>Name of the property from which to retrieve the total number of records in the dataset (defaults to <code>'total'</code>). </p>
     */
    totalProperty: 'total',

    //private
    initComponent : function(){
        //apply defaults into adapterConfig
        Ext.apply(this.adapterConfig, {
            xtype: 'w-' + this.adapter,
            border: false,
            tbar:[]
        });
        
        //apply defaults into component
        Ext.apply(this,{
            adapterConfig: this.adapterConfig,
            layout: 'fit',
            deferredRender: true
        });
        
        if (this.enableFilter){
            this.filter = new Wow.list.Search({
                //minChars:3
                autoFocus:true
                ,mode: this.filterMode
            });
            
            Ext.apply(this, {
               plugins:[this.filter]
            });
        }

        
        Wow.list.DataList.superclass.initComponent.call(this);
        
        this.addEvents(
            /**
             * @event beforeload
             * Fires before the data list store load.
             * @param {Wow.list.DataList} d self DataList.
             */
            'beforeload',
            /**
             * @event load
             * Fires after the data list store is loaded.
             * @param {Wow.list.DataList} d self DataList.
             */
            'load',
            /**
             * @event itemclick
             * Fires when a data list item is clicked.
             * @param {Ext.EventObject} e Event Object.
             */
            'itemclick',
            /**
             * @event itemdblclick
             * Fires when a data list item is dblclicked.
             * @param {Ext.EventObject} e Event Object.
             */
            'itemdblclick',
            /**
             * @event itemremoved
             * Fires after a data list item is removed in server side
             * @param {Ext.EventObject} e Event Object.
             */
            'itemremoved',
            /**
             * @event beforeitemremoved
             * Fires before a data list item is removed in server side
             * @param {Ext.EventObject} e Event Object.
             */
             'beforeitemremoved'
        );

        this.setStore();
        
    },

    //private
    onRender : function(ct, position){
        var list = this.createList();
        this.add(list);
        Wow.list.DataList.superclass.onRender.call(this, ct, position);
        this.getProperties();
    },

    //private
    afterRender : function(){
        Wow.list.DataList.superclass.afterRender.apply(this, arguments);
        
        if (this.ownerCt.actionsAlign == 'right'){
            this.tbar.add(['->'].concat(this.ownerCt.getActions()));
        }
        else
        if (this.ownerCt.actionsAlign == 'left'){
            this.tbar.insertButton(0,this.ownerCt.getActions().concat(['','']));

        }
        else{
            this.tbar.add(['-'].concat(this.ownerCt.getActions()));
        }

        this.registerEvents();
    },

    //private
    getProperties : function(){
        this.list = Ext.getCmp(this.id + '-adapter-list');
        this.adapter = Ext.getCmp(this.id + '-adapter');
        this.bbar = this.adapter.getBottomToolbar();
        this.tbar = this.adapter.getTopToolbar();
    },

    //private
    createList : function(){
        var adapter = {
            id: this.id + '-adapter',
            layout: 'fit',
            listConfig:{
                columns: this.columns,
                store: this.store,
                tpl: this.tpl,
                loadMask: true,
                maskDisabled: false
            }
        };

        if (this.enablePaging){
            Ext.apply(adapter, {
                bbar: this.getPaging()
            });
        }

        Ext.applyIf(adapter.listConfig, this.adapterConfig.listConfig);
        Ext.applyIf(adapter, this.adapterConfig);

        return adapter;

    },

    //private
    setStore : function(){
       /* try{
            this.store = this.adapterConfig.listConfig.store;

        }catch(e){
            if (!this.store){
                this.store = new Ext.data.JsonStore({
                    url: 'test.php',
                    root: this.root,
                    autoLoad: this.autoLoad,
                    idProperty: this.idProperty,
                    fields: this.fields
                });
            }
        }*/
    },

    //private
    getPaging : function(){
        return {
           xtype: 'paging',
           pageSize: this.pageSize,
           displayInfo: true,
           store: this.store
        };
    },
    
    //private
    registerEvents : function(){
        //register events to store
        this.store.on('load', function(){
            this.fireEvent('load', arguments);
        },this);
        this.store.on('beforeload', function(){
            this.fireEvent('beforeload', arguments);
        },this);
        
        //register events to list
        this.adapter.on('itemclick', function(){
            this.fireEvent('itemclick',arguments);
        },this);
        
        this.adapter.on('itemdblclick', function(){
            this.fireEvent('itemdblclick',arguments);
        },this);

        this.on('itemremoved',this.onItemRemoved,this);
        this.on('beforeitemremoved', this.onBeforeItemRemoved, this);
    },

    /**
     * <p>Gets only one selected record in grid.</p>
     * @param {Boolean} exception If it throw exception if not record selected
     * @return {Ext.data.Record/Boolean} Selected record or false if no record is selected
     */
    getSelected : function(exception){
        var record = this.adapter.getSelected();
        if (exception & !record){
            Wow.Msg.flash({
                msg: this.noSelectedRecordMsg,
                type: this.noSelectedRecordException
            });
        }
        return record;
    },
    /**
     * <p>Gets the selected records in grid.</p>
     * @param {Boolean} exception If it throw exception if not record selected
     * @return {Ext.data.Record[]/Boolean} Selected record or false if no record is selected
     */
    getSelections : function(exception){
        var records = this.adapter.getSelections();
        if (exception & records.length == 0){
            Wow.Msg.flash({
                msg: this.noSelectedRecordMsg,
                type: this.noSelectedRecordException
            });
            
            records = false;
        }
        return records;
    },
    /**
     * <p>Load data store.</p>
     * @params {Object} options An object containing properties which control loading options
     * @return void
     */
    load : function(options){
        this.store.load(options);
    },

    /**
     * <p>Remove Records from the Store</p>
     * @param {Ext.data.Record/Ext.data.Record[]} record Record to remove
     * @return void
     *
    removeRecord : function(record){
        this.store.remove(record);
    },*/

    reconfigure : function(){
        
    },

    //private
    destroyRecord : function(store, record, index){
        
    },
    
    //private
    onItemRemoved : function(){
        Wow.Msg.flash({
            msg: this.recordRemovedMsg,
            type: 'success'
        });        
    },

    //private
    onBeforeItemRemoved : function(store, rs, options, arg){
    }


});
Ext.reg('w-datalist', Wow.list.DataList);

