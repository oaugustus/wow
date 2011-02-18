//Define the namespace
Ext.ns('Wow');
/**
 * @class Wow.Mask
 * @author    Otavio Augusto R. Fernandes
 * @copyright (c) 2010, by Otavio Augusto R. Fernandes
 * @date      09. March 2010
 * @version   $Id: Msg.js 50 2010-02-22 16:34:25Z oaugusts $
 * <p>Utility class for mask a component while call a remove method.<p/>
 * <p>Example usage:</p>
 *<pre><code>
</code></pre>
 */
Wow.Mask = Ext.extend(Object, {
  /**
   * @cfg {String} waitingText
   * <p>The waiting text to show while mask is visible</p>
   */
   waitingText : 'Aguarde...',
  /**
   * @cfg {String} maskIconCls
   * <p>The mask icon css class</p>
   */
   maskIconCls : 'x-tbar-loading',

   //private
   constructor : function(config){
        config = config || {};
        Ext.apply(this, config);
   },

   /**
    * Initialize the plugin
    * @param parent The parent component
    */
   init : function(parent){
        this.parent = parent;

        //if store exists, handle show and hide automaticaly
        if (this.store){
            this.store.on('beforewrite', this.onStart, this);
            this.store.on('write', this.onStop, this);
        }
   },

   /**
    * Show the mask on it parent component
    */
   show : function(){
        Ext.app.maskTarget = this;
        if (this.parent){
            if (this.parent.title && this.parent.title != this.waitingText){
               this.originalText = this.parent.title;
               this.parent.setTitle(this.waitingText);

            }else
            if (this.parent.getText() != this.waitingText){
               this.originalText = this.parent.getText();
               this.parent.setText(this.waitingText);
            }

            if (this.parent.iconCls != this.maskIconCls){
                this.originalIconCls = this.parent.iconCls;
                this.parent.setIconClass(this.maskIconCls);
            }
            this.parent.disable();
        }       
   },

   /**
    * Hide the mask of the parent component
    */
   hide : function(){
        Ext.app.maskTarget = null;
        if (this.parent){
            if (this.originalIconCls != '' & this.originalIconCls != undefined){
                this.parent.setIconClass(this.originalIconCls);
            }
            else
                this.parent.setIconClass(null);

            if (this.parent.title){
                this.parent.setTitle(this.originalText);
            }else{
                this.parent.setText(this.originalText);
            }

            this.parent.enable();
        }       
   },

   //private
   onStart : function(store, action){
       if (action == this.action){
           this.show();
       }
   },

   //private
   onStop : function(store, action){
       if (action == this.action){
           this.hide();
       }
   }
});
