//Define the namespace
Ext.ns('Wow.data');
/**
 * @class Wow.Data
 * @author    Otavio Augusto R. Fernandes
 * @copyright (c) 2010, by Otavio Augusto R. Fernandes
 * @date      09. March 2010
 * @version   $Id: Msg.js 50 2010-02-22 16:34:25Z oaugusts $
 * <p>Utility class to provider data stores.<p/>
 * <p>Example usage:</p>
 *<pre><code>
</code></pre>
 */
Wow.data.Provider = function(){
    return {
        /**
         * Cria um DirectStore de acordo com as configurações recebidas
         * como parâmetro
         */
        createStore : function(cfg){
            Ext.applyIf(cfg,{
                baseParams : {},
                listeners  : {},
                autoLoad   : true
            });
            
            var dsStore = new Ext.data.DirectStore({
                xtype: 'directstore',                
                writer: new Ext.data.JsonWriter({
                  encode: false,
                  encodeDelete: true,
                  listfull: true,
                  writeAllFields: false
                }),
                proxy: new Ext.data.DirectProxy({
                   directFn: cfg.directFn,
                   api: cfg.api ? cfg.api : {},
                   method: 'POST'
                }),
                totalProperty: 'total',
                root: 'records',
                autoLoad: cfg.autoLoad,
                autoSave: false,
                fields: cfg.fields,
                idProperty: 'id',
                paramsAsHash:false,
                listeners: cfg.listeners,
                baseParams:cfg.baseParams
            });

            return dsStore;
        },

        /**
         * Cria um GroupingStore de acordo com as configurações recebidas
         * como parâmetro
         */
        createGroupingStore : function(cfg){
            if (!cfg.baseParams){
                cfg.baseParams = {};
            }

            if (!cfg.listeners){
                cfg.listeners = {};
            }

            var dsStore = new Ext.data.GroupingStore({
                xtype: 'groupingstore',
                writer: new Ext.data.JsonWriter({
                  encode: false,
                  encodeDelete: true,
                  listfull: true,
                  writeAllFields: false
                }),
                proxy: new Ext.data.DirectProxy({
                   directFn: cfg.directFn,
                   api: cfg.api ? cfg.api : {},
                   method: 'POST'
                }),
                reader: new Ext.data.JsonReader({
                    totalProperty: 'total',
                    root: 'records',
                    fields: cfg.fields,
                    idProperty: 'id'                    
                }),
                autoLoad: cfg.autoLoad,
                autoSave: false,
                paramsAsHash:false,
                listeners: cfg.listeners,
                baseParams:cfg.baseParams,
                sortInfo: cfg.sortInfo,
                groupField: cfg.groupField
            });

            return dsStore;
        },

        /**
         * Cria um ArrayStore de acordo com as configurações recebidas
         * como parâmetro.
         */
        createLocalStore : function (cfg){
            var localStore = new Ext.data.ArrayStore({
                xtype: 'arraystore',
                fields: cfg.fields,
                data: cfg.data,
                idIndex: cfg.idIndex
            });

            return localStore;

        }
    }
}();
