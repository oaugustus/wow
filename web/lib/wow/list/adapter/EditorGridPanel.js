/*!
 * Web Office - Workspace
 * @author Otávio Augusto R. Fernandes - oaugustus
 * Copyright(c) 2010 Net On
 */
Ext.ns('Wow.list.adapter');

/**
 * @class Wow.list.adapter.EditorGridPanel
 * @extends Ext.Panel
 * <p>EditorGridAdapter to datalist. </p>
 * @constructor
 * @param {Object} config The config object
 * @xtype w-editorgrid
 */
Wow.list.adapter.EditorGridPanel = Ext.extend(Ext.Panel, {
    /**
     * @cfg {Object} layout
     * <p>The layout manager (defaults to <code>'fit'</code>)</p>
     */
    layout: 'fit',
    /**
     * @cfg {EventObject} editKey
     * <p>The key used in key map to start a cell edit</code>)</p>
     */
    editKey: Ext.EventObject.F2,
    /**
     * @cfg {EventObject} saveKey
     * <p>The key used in key map to save changes</code>)</p>
     */
    saveKey: Ext.EventObject.F12,
    /**
     * @cfg {EventObject} delKey
     * <p>The key used in key map to delete record</code>)</p>
     */
    delKey: Ext.EventObject.DELETE,

    //private
    initComponent : function(){
        var list = {
            id: this.id + '-list',
            xtype: 'editorgrid',
            border: false,
            loadMask: true
        };

        Ext.applyIf(list, this.listConfig);

        this.items = list;

        Wow.list.adapter.EditorGridPanel.superclass.initComponent.call(this);

        this.addEvents(
            /**
             * @event itemclick
             * Fires when a row is clicked.
             * @param {Ext.EventObject} e Event Object.
             */
            'itemclick',
            /**
             * @event itemdblclick
             * Fires when a row is dblclicked.
             * @param {Ext.EventObject} e Event Object.
             */
            'itemdblclick'            
        );
        
    },
    //private
    afterRender : function(){
        Wow.list.adapter.EditorGridPanel.superclass.afterRender.apply(this, arguments);
        this.list = Ext.getCmp(this.id + '-list');
        this.registerEvents();
        this.mapKeys();
    },

    //private
    registerEvents : function(){
        //register fire events
        this.list.on('rowclick',function(){
            this.fireEvent('itemclick',arguments);
        },this);
        this.list.on('rowdblclick',function(){
            this.fireEvent('itemdblclick',arguments);
        },this);
        
    },

    //private
    mapKeys : function (){
        var editMap = new Ext.KeyMap(this.body, {
           key: this.editKey,
           fn: this.startCellEdit,
           scope: this
        });

        var saveMap = new Ext.KeyMap(this.body, {
           key: this.saveKey,
           //ctrl: true,
           fn: this.save,
           scope: this
        });

        var delMap = new Ext.KeyMap(this.body, {
           key: this.delKey,
           fn: this.remove,
           scope: this
        });


    },
    
    /**
     * <p>Gets only one selected record in grid.</p>
     * @return Ext.data.Record/Boolean Selected record or false if no record is selected
     */
    getSelected : function(){
        var index = this.list.getSelectionModel().getSelectedCell();
        var record = false;
        
        if (index) {
            record = this.list.store.getAt(index[0]);
        }

        return record;
    },
    /**
     * <p>Gets the selected records in grid.</p>
     * @return Ext.data.Record[]/Boolean Selected record or false if no record is selected
     */
    getSelections : function(){
        return this.getSelected();
    },
    /**
     * <p>Gets the selected cell in grid.</p>
     * @return Ext.data.Record[]/Boolean Selected record or false if no record is selected
     */
    getSelectedCell : function(){
       var cell = this.list.getSelectionModel().getSelectedCell();
       return cell;
    },

    //private
    startCellEdit : function(){
        var cell = this.getSelectedCell();
        if (cell){
            this.list.startEditing(cell[0],cell[1]);
        }
    },

    //private
    save : function(){
        var module = Ext.getCmp(this.id.split('-')[0]);
        module.executeUpdate();
    },
    
    //private
    remove : function(){
        var module = Ext.getCmp(this.id.split('-')[0]);
        module.executeRemove(this.getSelected());
    }

});

Ext.reg('w-editorgrid',Wow.list.adapter.EditorGridPanel);

