/**
 * Search plugin for DataList
 *
 * @author    Otavio Augusto R. Fernandes
 * @copyright (c) 2010, by Otavio Augusto R. Fernandes
 * @date      15. February 2010
 *
 * Based on Ext.ux.grid.Search by Ing. Jozef Sakalos avaiable in (http://gridsearch.extjs.eu/)
 */
/**
 * @class Wow.list.Search
 * @extends Ext.util.Observable
 * @param {Object} config configuration object
 * @constructor
 */
Wow.list.Search = function(config) {
    Ext.apply(this, config);
    Wow.list.Search.superclass.constructor.call(this);
};

/**
 * @class Wow.list.Search
 * @extends Ext.util.Observable
 * @param {Object} config configuration object
 * @constructor
 */
Ext.extend(Wow.list.Search, Ext.util.Observable, {
    /**
     * @cfg {String} align Align to apply into filter on the tbar os his container
     */
     align: 'left',
    /**
     * @cfg {String} applyFilterText Text to display in tooltip of apply button
     */
     applyFilterText: 'Aplicar filtro',
    /**
     * @cfg {String} clearFilterText Text to display in tooltip of apply button
     */
     clearFilterText: 'Limpar filtro',
    /**
     * @cfg {Boolean} autoFocus true to try to focus the input field on each store load (defaults to undefined)
     */
    /**
     * @cfg {String} emptyFilterText Text to display in empty filter exception
     */
     emptyFilterText: 'O filtro n&atilde;o pode ser vazio',
    /**
     * @cfg {String} emptyFilterException Type of thrown exception
     */
     emptyFilterException: 'warning',
    /**
     * @cfg {String} encodeFields Execute Ext.encode before send fields params
     */
     encodeFields: false,
    /**
     * @cfg {String} searchText Text to display on menu button
     */
     searchText:'Filtrar'
    /**
     * @cfg {String} searchTipText Text to display as input tooltip. Set to '' for no tooltip
     */
    ,searchTipText:'Digite o texto a procurar'

    /**
     * @cfg {String} selectAllText Text to display on menu item that selects all fields
     */
    ,selectAllText:'Selecionar todos'

    /**
     * @cfg {String} iconCls Icon class for menu button (defaults to icon-magnifier)
     */
    ,iconCls:'wow-finder'

    /**
     * @cfg {String/Array} checkIndexes Which indexes to check by default. Can be either 'all' for all indexes
     * or array of dataIndex names, e.g. ['persFirstName', 'persLastName']
     */
    ,checkIndexes:'all'

    /**
     * @cfg {Array} disableIndexes Array of index names to disable (not show in the menu), e.g. ['persTitle', 'persTitle2']
     */
    ,disableIndexes:[]

    /**
     * @cfg {Ext.util.MixedCollection} advancedFilters Array of index names to disable (not show in the menu), e.g. ['persTitle', 'persTitle2']
     */
    ,advancedFilters: null

    /**
     * @cfg {String} dateFormat how to format date values. If undefined (the default)
     * date is formatted as configured in colummn model
     */
    ,dateFormat:undefined

    /**
     * @cfg {String} filterMinChars
     */
    ,filterMinChars: 3

    /**
     * @cfg {Boolean} showSelectAll Select All item is shown in menu if true (defaults to true)
     */
    ,showSelectAll:false

    /**
     * @cfg {String} menuStyle Valid values are 'checkbox' and 'radio'. If menuStyle is radio
     * then only one field can be searched at a time and selectAll is automatically switched off.
     */
    ,menuStyle:'checkbox'

    /**
     * @cfg {Number} minChars minimum characters to type before the request is made. If undefined (the default)
     * the trigger field shows magnifier icon and you need to click it or press enter for search to start. If it
     * is defined and greater than 0 then maginfier is not shown and search starts after minChars are typed.
     */

    /**
     * @cfg {String} minCharsTipText Tooltip to display if minChars is > 0
     */
    ,minCharsTipText:'Digite ao menos {0} caracteres'

    /**
     * @cfg {String} mode Use 'remote' for remote stores or 'local' for local stores. If mode is local
     * no data requests are sent to server the grid's store is filtered instead (defaults to 'remote')
     */
    ,mode:'remote'

    /**
     * @cfg {Array} readonlyIndexes Array of index names to disable (show in menu disabled), e.g. ['persTitle', 'persTitle2']
     */

    /**
     * @cfg {Number} width Width of input field in pixels (defaults to 230)
     */
    ,width:230

    /**
     * @cfg {String} xtype xtype is usually not used to instantiate this plugin but you have a chance to identify it
     */
    ,xtype:'gridsearch'

    /**
     * @cfg {Object} paramNames Params name map (defaults to {fields:'fields', query:'query'}
     */
    ,paramNames: {
             fields:'fields'
            ,query:'query'
    }

    /**
     * @cfg {String} shortcutKey Key to fucus the input field (defaults to r = Sea_r_ch). Empty string disables shortcut
     */
    ,shortcutKey:'r'

    /**
     * @cfg {String} shortcutModifier Modifier for shortcutKey. Valid values: alt, ctrl, shift (defaults to alt)
     */
    ,shortcutModifier:'alt'

    /**
     * @cfg {String} align 'left' or 'right' (defaults to 'left')
     */

    /**
     * @cfg {Number} minLength force user to type this many character before he can make a search
     */

    /**
     * @cfg {Ext.Panel/String} toolbarContainer Panel (or id of the panel) which contains toolbar we want to render
     * search controls to (defaults to this.datalist, the grid this plugin is plugged-in into)
     */

    // {{{
    /**
     * private
     * @param {Wow.list.DataList} datalist reference to datalist this plugin is used for
     */
    ,init:function(datalist) {
        this.datalist = datalist;
        this.advancedFilters = new Ext.util.MixedCollection();

        this.hasSearch = false;

        // do our processing after grid render and reconfigure
        datalist.onRender = datalist.onRender.createSequence(this.onRender, this);
        datalist.reconfigure = datalist.reconfigure.createSequence(this.reconfigure, this);
    }

    /**
     * private add plugin controls to <b>existing</b> toolbar and calls reconfigure
     */
    ,onRender:function() {
            var tb = this.datalist.tbar;

            // create the search menu
            this.menu = new Ext.menu.Menu();

            // adds search button
            this.addFilterButton(tb);

            // adds the search field
            this.createSearchField(tb);            

            // create the filters menu
            this.createFiltersMenu();

            // keyMap
            if(this.shortcutKey && this.shortcutModifier) {
                    var shortcutEl = this.datalist.getEl();
                    var shortcutCfg = [{
                             key:this.shortcutKey
                            ,scope:this
                            ,stopEvent:true
                            ,fn:function() {
                                    this.field.focus();
                            }
                    }];
                    shortcutCfg[0][this.shortcutModifier] = true;
                    this.keymap = new Ext.KeyMap(shortcutEl, shortcutCfg);
            }

            if(true === this.autoFocus) {
                    this.datalist.store.on({scope:this, load:function(){this.field.focus();}});
            }
    }
    /**
     * Adds filter button in the list toolbar
     */
    ,addFilterButton : function(tb){
        if (this.align == 'left'){
            // add menu button
            tb.insert(0,{
                 text:this.searchText
                ,menu:this.menu
                ,iconCls:this.iconCls
                ,itemId: 'search-button'
            });

        }else{
            tb.addFill();
            // add menu button
            tb.add({
                 text:this.searchText
                ,menu:this.menu
                ,iconCls:this.iconCls
                ,itemId: 'search-button'
            });

        }
        
    }

    /**
     * Create and setup de search field
     */
    ,createSearchField : function(tb){
        // add input field (TwinTriggerField in fact)
        this.field = new Ext.form.TwinTriggerField({
                 width:this.width
                ,selectOnFocus:undefined === this.selectOnFocus ? true : this.selectOnFocus
                ,trigger1Class:'x-form-clear-trigger'
                ,hideTrigger1: true
                ,trigger2Class:this.minChars ? 'x-hidden' : 'x-form-search-trigger'
                ,onTrigger1Click:this.minChars ? Ext.emptyFn : this.onTriggerClear.createDelegate(this)
                ,onTrigger2Click:this.onTriggerSearch.createDelegate(this)
                ,minLength:this.minLength
                ,visible: false
        });


        // install event handlers on input field
        this.field.on('render', function() {
                //this.field.el.dom.qtip = this.minChars ? String.format(this.minCharsTipText, this.minChars) : this.searchTipText;
                new Ext.ToolTip({
                  target: this.field.el.dom,
                  html: this.minChars ? String.format(this.minCharsTipText, this.minChars) : this.searchTipText,
                  anchor: 'bottom'
                });

                if(this.minChars) {
                        this.field.el.on({scope:this, buffer:300, keyup:this.onKeyUp});
                }

                Ext.QuickTips.init();

                new Ext.ToolTip({
                 target: this.field.triggers[0],
                 html: this.clearFilterText,
                 anchor: 'left'
                });

                new Ext.ToolTip({
                 target: this.field.triggers[1],
                 anchor: 'left',
                 html: this.applyFilterText
                });


                // install key map
                var map = new Ext.KeyMap(this.field.el, [{
                         key:Ext.EventObject.ENTER
                        ,scope:this
                        ,fn:this.onTriggerSearch
                },{
                         key:Ext.EventObject.ESC
                        ,scope:this
                        ,fn:this.onTriggerClear
                }]);
                map.stopEvent = true;
        }, this, {single:true});

        //tb.add(this.field);
        
    }
    /**
     * field el keypup event handler. Triggers the search
     * @private
     */
    ,onKeyUp:function() {
            var length = this.field.getValue().toString().length;
            if(0 === length || this.minChars <= length) {
                    this.onTriggerSearch();
            }
    } 
    /**
     * private Clear Trigger click handler
     */
    ,onTriggerClear:function() {
            this.field.triggers[0].hide();
            if (this.hasSearch){
                this.hasSearch = false;

                if ('local' === this.mode)
                    this.datalist.store.clearFilter();
                else{
                    delete(this.datalist.store.baseParams[this.paramNames.fields]);
                    delete(this.datalist.store.baseParams[this.paramNames.query]);
                    this.datalist.store.load();
                }
                    
            }
            
            if(this.field.getValue() != '') {
                this.field.setValue('');
                this.field.focus();
                this.onTriggerSearch(false);
            }
    }
    /**
     * private Search Trigger click handler (executes the search, local or remote)
     */
    ,onTriggerSearch:function(find) {
        if (!this.hasSearch && find){
            this.field.triggers[0].show();
            this.hasSearch = true;
        }

        var val = this.field.getValue();
        var store = this.datalist.store;

        var advFilter = this.buildQuery(this.getFilterData());

        // grid's store filter
        if('local' === this.mode) {
                store.clearFilter();
                if(val) {
                        store.filterBy(function(r) {
                                var retval = false;
                                this.menu.items.each(function(item) {
                                        if(!item.checked || retval) {
                                                return;
                                        }
                                        var rv = r.get(item.dataIndex);
                                        rv = rv instanceof Date ? rv.format(this.dateFormat || r.fields.get(item.dataIndex).dateFormat) : rv;
                                        var re = new RegExp(val, 'gi');
                                        retval = re.test(rv);
                                }, this);
                                if(retval) {
                                        return true;
                                }
                                return retval;
                        }, this);
                }
                else {
                    //@todo aplicar filtro local avançado
                }
        }
            // ask server to filter records
            else {
                // clear start (necessary if we have paging)
                if(store.lastOptions && store.lastOptions.params) {
                    store.lastOptions.params[store.paramNames.start] = 0;
                }

                // get fields to search array
                var fields = [];
                this.menu.items.each(function(item) {
                    if(item.checked && item.dataIndex) {
                        if (!item.advancedFilter.active)
                            if (item.filterIndex)
                                fields.push(item.filterIndex);
                            else
                                fields.push(item.dataIndex);
                    }
                });

                // add fields and query to baseParams of store
                delete(store.baseParams[this.paramNames.fields]);
                delete(store.baseParams[this.paramNames.query]);
                if (store.lastOptions && store.lastOptions.params) {
                    delete(store.lastOptions.params[this.paramNames.fields]);
                    delete(store.lastOptions.params[this.paramNames.query]);
                }
                
                if(fields.length) {
                    var basic = {};
                    basic[this.paramNames.query] = val;
                    
                    if (this.encodeFields)
                       basic[this.paramNames.fields] = Ext.encode(fields);
                    else
                       basic[this.paramNames.fields] = fields;
                    
                    store.baseParams['basic'] = basic;

                }

                store.baseParams['advanced'] = advFilter;

                // reload store
                store.load();
            }

    } 
    /**
     * 
     */
    ,setDisabled : function() {
            this.field.setDisabled.apply(this.field, arguments);
    }
    /**
     * Enable search (TwinTriggerField)
     */
    ,enable:function() {
            this.setDisabled(false);
    } 
    /**
     * Enable search (TwinTriggerField)
     */
    ,disable:function() {
            this.setDisabled(true);
    }
    /**
     * private (re)configures the plugin, creates menu items from column model
     */
    ,createFiltersMenu : function() {
        // remove old items
        var menu = this.menu;
        menu.removeAll();
        
        // add Select All item plus separator
        if(this.showSelectAll && 'radio' !== this.menuStyle) {
            menu.add(
                this.createSelectAllOption()
                ,'-'
            );
        }

        // add new items
        var cm = this.datalist.columns;
        var fields = this.datalist.store.fields;
        var group = undefined;
        if('radio' === this.menuStyle) {
            group = 'g' + (new Date).getTime();
        }
        var hasFilter = false;
        
        Ext.each(cm, function(config) {
            if(config.header && config.dataIndex && config.filterable) {
                hasFilter = true;
                var currentField = fields.get(config.dataIndex);

                var filterClass = this.getFilterClass(config.filter ? config.filter.type : currentField.type.type);
                var advFilter = new filterClass(config);
                advFilter.on('activate',this.activateFilter,this);
                advFilter.on('update',this.activateFilter,this);
                advFilter.on('deactivate',this.deactivateFilter,this);

                var dataIndex = config.filterIndex ? config.filterIndex : config.dataIndex;
                
                menu.add(new Ext.menu.CheckItem({
                     text:config.header
                    ,hideOnClick:false
                    ,group:group
                    ,listeners:{
                        'checkchange' : function(item, checked){
                            if (!checked){
                                this.deactivateFilter(item.advancedFilter);
                            }else{
                                this.activateFilter(item.advancedFilter);
                            }
                        },
                        scope: this
                    }
                    //,checked:'all' === this.checkIndexes
                    ,dataIndex: dataIndex
                    ,advancedFilter: advFilter
                    ,menu: advFilter.menu
                }));
            }
        }, this);

        if (!hasFilter){
            this.datalist.tbar.getComponent('search-button').disable();
            this.field.disable();
        }else{
            this.datalist.tbar.getComponent('search-button').enable();
            this.field.enable();
        }

        // check items
        if(this.checkIndexes instanceof Array) {
            Ext.each(this.checkIndexes, function(di) {
                var item = menu.items.find(function(itm) {
                        return itm.dataIndex === di;
                });
                if(item) {
                        item.setChecked(true, true);
                }
            }, this);
        }
        // disable items
        if(this.readonlyIndexes instanceof Array) {
            Ext.each(this.readonlyIndexes, function(di) {
                var item = menu.items.find(function(itm) {
                        return itm.dataIndex === di;
                });
                if(item) {
                        item.disable();
                }
            }, this);
        }
            

    }

    ,activateFilter : function(filter){
        //console.info('activateFilter');
        var rootMenu = filter.menu.ownerCt;
        this.hasFilter = true;
        if (filter.getValue().length >= this.filterMinChars && filter.type == 'string')
        {
            rootMenu.addClass('active-filter');
            rootMenu.setChecked(true);
            this.advancedFilters.add(filter.id, filter);
            this.onTriggerSearch();
        }else{
            rootMenu.addClass('active-filter');
            rootMenu.setChecked(true);
            this.advancedFilters.add(filter.id, filter);
            this.onTriggerSearch();            
        }
    }

    ,deactivateFilter : function(filter){
        this.hasFilter = false;
        var rootMenu = filter.menu.ownerCt;
        rootMenu.setChecked(false);
        rootMenu.removeClass('active-filter');
        this.advancedFilters.remove(this.advancedFilters.item(filter.id));
        this.onTriggerSearch();
    }

    /**
     * Returns an Array of the currently active filters.
     * @return {Array} filters Array of the currently active filters.
     */
    ,getFilterData : function () {
        var filters = [], i, len;

        this.advancedFilters.each(function (f) {
            if (f.active) {
                var d = [].concat(f.serialize());
                for (i = 0, len = d.length; i < len; i++) {
                    filters.push({
                        field: f.filterIndex ? f.filterIndex : f.dataIndex,
                        data: d[i]
                    });
                }
            }
        });

        if (filters.length > 0){
            this.menu.ownerCt.setText('<b><i>' + this.searchText + ' *</i></b>');
        }else{
            this.menu.ownerCt.setText(this.searchText);
        }
        
        return filters;
    }
    
    /**
     * create the select all options in filters menu
     */
    ,createSelectAllOption : function(){
        return new Ext.menu.CheckItem({
             text:this.selectAllText
            ,checked:!(this.checkIndexes instanceof Array)
            ,hideOnClick:false
            ,handler:function(item) {
                var checked = ! item.checked;
                item.parentMenu.items.each(function(i) {
                    if(item !== i && i.setChecked && !i.disabled) {
                        i.setChecked(checked);
                    }
                });
            },
            scope: this
        })
    }

    /**
     * Function for locating filter classes, overwrite this with your favorite
     * loader to provide dynamic filter loading.
     * @param {String} type The type of filter to load ('Filter' is automatically
     * appended to the passed type; eg, 'string' becomes 'StringFilter').
     * @return {Class} The Ext.ux.grid.filter.Class
     */
    ,getFilterClass : function (type) {
        type = type.toLowerCase();
        
        // map the supported Ext.data.Field type values into a supported filter
        switch(type) {
            case 'numeric':
            case 'integer':
            case 'int':
              type = 'Numeric';
              break;
            case 'float':
              type = 'Float';
              break;
            case 'bool':
            case 'boolean':
                type = 'Boolean';
                break;
            case 'date':
                type = 'Date';
                break;
            case 'list':
                type = 'List';
                break;
            default:
                type = 'String';
        }
        return Wow.list.search[type + 'Filter'];
    }

    /**
     * Function to take the active filters data and build it into a query.
     * The format of the query depends on the <code>{@link #encode}</code>
     * configuration:
     * <div class="mdetail-params"><ul>
     *
     * <li><b><tt>false</tt></b> : <i>Default</i>
     * <div class="sub-desc">
     * Flatten into query string of the form (assuming <code>{@link #paramPrefix}='filters'</code>:
     * <pre><code>
filters[0][field]="someDataIndex"&
filters[0][data][comparison]="someValue1"&
filters[0][data][type]="someValue2"&
filters[0][data][value]="someValue3"&
     * </code></pre>
     * </div></li>
     * <li><b><tt>true</tt></b> :
     * <div class="sub-desc">
     * JSON encode the filter data
     * <pre><code>
filters[0][field]="someDataIndex"&
filters[0][data][comparison]="someValue1"&
filters[0][data][type]="someValue2"&
filters[0][data][value]="someValue3"&
     * </code></pre>
     * </div></li>
     * </ul></div>
     * Override this method to customize the format of the filter query for remote requests.
     * @param {Array} filters A collection of objects representing active filters and their configuration.
     *    Each element will take the form of {field: dataIndex, data: filterConf}. dataIndex is not assured
     *    to be unique as any one filter may be a composite of more basic filters for the same dataIndex.
     * @return {Object} Query keys and values
     */
    ,buildQuery : function (filters) {
        var p = [], i, f, root, dataPrefix, key, tmp, fCfg,
            len = filters.length;

        if (!this.encode){
            for (i = 0; i < len; i++) {
                f = filters[i];
                fCfg = {};
                fCfg['field'] = f.field;
                fCfg['type'] = f.data.type;
                fCfg['value'] = f.data.value;
                if (f.data.comparison)
                    fCfg['comparison'] = f.data.comparison;

                p.push(fCfg);
            }
        } else {
            tmp = [];
            for (i = 0; i < len; i++) {
                f = filters[i];
                tmp.push(Ext.apply(
                    {},
                    {field: f.field},
                    f.data
                ));
            }
            // only build if there is active filter
            if (tmp.length > 0){
                p[this.paramPrefix] = Ext.util.JSON.encode(tmp);
            }
        }
        return p;
    }


}); 