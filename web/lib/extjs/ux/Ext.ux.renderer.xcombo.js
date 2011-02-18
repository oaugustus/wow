Ext.ns("Ext.ux.renderer");

Ext.ux.renderer.XComboRenderer = function(options) {
    var value = options.value;
    var combo = options.combo;

    var returnValue = value;
    var valueField = combo.valueField;
        
    var idx = combo.store.findBy(function(record) {
        if(record.get(valueField) == value) {
            returnValue = record.get(combo.displayField);
            return true;
        }
    });

    var loadRecord = function(prop, value, combo){
        if (value != ''){
            //var ct = combo.refOwner ? combo.refOwner.getEl() : combo.ownerCt.getEl();
            combo.store.load({
                params:{
                    fields:[prop],
                    query: value
                },
                callback : function(rec){
                   //mask.hide();
                       Ext.ux.renderer.XComboRenderer(options);
                },
                scope: this
            });

        }
    };

    if(idx < 0 && value == 0) {
        loadRecord(combo.displayField, valueField, combo);
    }

    return returnValue;
};

Ext.ux.renderer.XCombo = function(combo) {
    return function(value, meta, record) {
        return Ext.ux.renderer.XComboRenderer({value: value, meta: meta, record: record, combo: combo});
    };
}