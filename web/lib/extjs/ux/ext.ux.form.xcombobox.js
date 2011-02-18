/**
 * Ext.ux.form.XComboBox - ComboBox that load record value if it not found
 *
 * @author  Otávio Augusto R. Fernandes
 *
 * @license Ext.ux.form.XComboBox is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * License details: http://www.gnu.org/licenses/lgpl.html
 */

/*global Ext */

Ext.ns('Ext.ux.form');

/**
  * @class Ext.ux.form.XComboBox
  * @extends Ext.form.ComboBox
  */
Ext.ux.form.XComboBox = Ext.extend(Ext.form.ComboBox,{
    /**
     * Sets the specified value into the field.  If the value finds a match, the corresponding record text
     * will be displayed in the field.  If the value does not match the data value of an existing item,
     * and the valueNotFoundText config option is defined, it will be displayed as the default field text.
     * Otherwise the field will be blank (although the value will still be set).
     * @param {String} value The value to match
     * @return {Ext.form.Field} this
     */
    setValue : function(v){
        var text = v;
        if(this.valueField){
            var r = this.findRecord(this.valueField, v);
            if(r){
                text = r.data[this.displayField];
            }else {
                this.loadRecord(this.valueField, v);
            }
        }
        this.lastSelectionText = text;
        if(this.hiddenField){
            this.hiddenField.value = Ext.value(v, '');
        }
        Ext.form.ComboBox.superclass.setValue.call(this, text);
        this.value = v;
        return this;
    },

    /**
     * Loads datastore with the record value
     */
    loadRecord : function(prop, value){
        if (value != ''){
            // try set a mask
            try{
                var ct = this.refOwner ? this.refOwner.getEl() : this.ownerCt.getEl();
                var mask = new Ext.LoadMask(ct, {msg:this.loadingText,removeMask:true});
                mask.show();
            }catch(e){}
            
            this.store.load({
                params:{
                    fields:[prop],
                    query: value
                },
                callback : function(rec){
                    if (mask){
                       mask.hide();
                    }
                    if (rec.length > 0){
                        this.setValue(rec[0].data.id);
                    }else if(Ext.isDefined(this.valueNotFoundText)){
                        Ext.ux.form.XComboBox.superclass.setValue.call(this, value);
                    }
                },
                scope: this
            });

        }
    }
})

Ext.reg('xcombo',Ext.ux.form.XComboBox);