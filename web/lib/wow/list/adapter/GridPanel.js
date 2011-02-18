/*!
 * Web Office - Workspace
 * @author Otávio Augusto R. Fernandes - oaugustus
 * Copyright(c) 2010 Net On
 */
Ext.ns('Wow.list.adapter');

/**
 * @class Wow.list.adapter.GridPanel
 * @extends Ext.Panel
 * <p>GridAdapter to datalist. </p>
 * @constructor
 * @param {Object} config The config object
 * @xtype w-grid
 */
Wow.list.adapter.GridPanel = Ext.extend(Ext.Panel, {
    /**
     * @cfg {Object} layout
     * <p>The layout manager (defaults to <code>'fit'</code>)</p>
     */
    layout: 'fit',
    /**
     * @cfg {EventObject} delKey
     * <p>The key used in key map to delete record</code>)</p>
     */
    delKey: Ext.EventObject.DELETE,

    //private
    initComponent : function(){
        var list = {
            id: this.id + '-list',
            xtype: 'grid',
            border: false,
            maskDisabled: false
        };

        Ext.applyIf(list, this.listConfig);

        list = this.buildView(list);

        this.items = list;

        Wow.list.adapter.GridPanel.superclass.initComponent.call(this);

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

    // private
    buildView : function(list){
      if (list.viewConfig.xtype){
          if (list.viewConfig.xtype == 'groupingview'){
            list.view = new Ext.grid.GroupingView(list.viewConfig);
            delete(list.viewConfig);
          }            
      }

      return list;
    },
    
    //private
    afterRender : function(){
        Wow.list.adapter.GridPanel.superclass.afterRender.apply(this, arguments);
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
        var record = false;

        if (this.list.selModel instanceof Ext.grid.RowSelectionModel) {
            record = this.list.selModel.getSelected();
        }
        else if (this.list.selModel instanceof Ext.grid.CellSelectionModel) {
            var selectedCell = this.list.selModel.getSelectedCell();
            record = this.list.store.getAt(selectedCell[0]);
        }

        return record;

    },
    /**
     * <p>Gets the selected records in grid.</p>
     * @return Ext.data.Record[]/Boolean Selected record or false if no record is selected
     */
    getSelections : function(){
        var records = false;
        
        if (this.list.selModel instanceof Ext.grid.RowSelectionModel) {
            records = this.list.getSelectionModel().getSelections();
        }else if (this.list.selModel instanceof Ext.grid.CellSelectionModel) {
            var selectedCell = this.list.selModel.getSelectedCell();
            records = [this.list.store.getAt(selectedCell[0])];
        }

        return records;
        
    },
    //private
    remove : function(){
        var module = Ext.getCmp(this.id.split('-')[0]);
        module.executeRemove(this.getSelected());
    }

});

Ext.reg('w-grid',Wow.list.adapter.GridPanel);

