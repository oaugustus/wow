/*!
 * Web Office - Workspace
 * @author Otávio Augusto R. Fernandes - oaugustus
 * Copyright(c) 2010 Net On
 */
Ext.ns('Wow.form');
/**
 * @class Wow.form.DataForm
 * @extends Ext.Window
 * <p>Encapsulates all useful functionalities related to form,
 * to the Wow's module. </p>
 * @constructor
 * @param {Object} config The config object
 * @xtype w-dataform
 */
Wow.form.DataForm = Ext.extend(Ext.Window,{
    /**
     * FormPanel Component
     * <pThe FormPanel component reference</p>
     * @type Ext.FormPanel
     * @property form
     */
    /**
     * Record edited
     * <pThe edited record</p>
     * @type Ext.data.Record
     * @property record
     */
    /**
     * @cfg {String} ftype
     * <p>Form xtype, if defined the form will be created from it.</p>
     */
    /**
     * @cfg {String} invalidFormSaveMsg
     * <p>Error message to display into flash message to inform invalid form.</p>
     */
    invalidFormSaveMsg: 'Os campos em destaque s&atilde;o obrigat&oacute;rios!',
    /**
     * @cfg {String} confirmCloseTitle
     * <p>Confirm close title.</p>
     */
    confirmCloseTitle: 'Aten&ccedil;&atilde;o',
    /**
     * @cfg {String} confirmCloseMsg
     * <p>Confirm close message.</p>
     */
    confirmCloseMsg: 'Deseja cancelar este cadastro?',
    /**
     * @cfg {String} successSaveMsg
     * <p>Success save record message.</p>
     */
    successSaveMsg: 'O registro foi salvo com &ecirc;xito!',
    /**
     * @cfg {Wow.Module} module
     * <p>Reference to the owner module of this data form.</p>
     */
    /**
     * @cfg {String} waitingSaveText
     * <p>Message to display wait save the record.</p>
     */
    waitingSaveText: 'Salvando...',
    /**
     * @cfg {String} keepOpenText
     * <p>.</p>
     */
    keepOpenedText: 'Manter esta janela aberta',
    /**
     * @cfg {String} createTitle
     * <p>Title to display in the window while creating record.</p>
     */
    /**
     * @cfg {String} editTitle
     * <p>Title to display in the window while editing record.</p>
     */
    /**
     * @cfg {String} saveButtonText
     * <p>Text to display in save button.</p>
     */
    saveButtonText: '<b>Salvar</b>',
    /**
     * @cfg {String} cancelButtonText
     * <p>Text to display in cancel button.</p>
     */
    cancelButtonText: 'Cancelar',

    /**
     * @cfg {String} showKeepOpener
     * <p>Show the keep opener check or not.</p>
     */
    showKeepOpener : true,
    
    /**
     * @private
     */
    initComponent : function(){

       Ext.apply(this,{
           layout: 'fit',
           autoHeight: true,
           buttonAlign: 'left',
           closeAction: 'hide',
           closable: false
       });
       
       //Create defaults buttons
       this.fbar =  this.getDefaultButtons();

       //Create the forms
       this.items = [
           this.createForm()
       ];

        this.addEvents(
            'edit',
            'create',
            'beforesave',
            'save'
        );

       Wow.form.DataForm.superclass.initComponent.apply(this, arguments);
   },

   /**
    * @private
    */
   afterRender : function(){
      Wow.form.DataForm.superclass.afterRender.apply(this, arguments);
      this.getProperties();
   },

   /**
    * Gets object properties
    * @private
    */
   getProperties : function(){
       this.form = this.getComponent('form');
       this.saveButton = this.fbar.getComponent('saveButton');
       this.cancelButton = this.fbar.getComponent('cancelButton');
       this.keepOpenedCheck = this.fbar.getComponent('keepOpenedCheck')
   },

   /**
    * @private
    */
   getDefaultButtons : function(){
       
       var defaultButtons = [
           {
               xtype: 'checkbox',
               hideLabel: true,
               boxLabel: this.keepOpenedText,
               itemId: 'keepOpenedCheck'
           },'->',
           {
               text: this.saveButtonText,
               scope: this,
               handler: this.executeSave,
               plugins: [new Wow.Mask({waitingText: this.waitingSaveText, itemId: 'mask'})],
               itemId: 'saveButton'
           },
           {
               text: this.cancelButtonText,
               scope: this,
               handler: this.cancel,
               itemId: 'cancelButton'
           }
       ];

       return defaultButtons;
   },

   /**
    * Open the form to create a new record
    */
   create : function(){       
       this.setTitle(this.createConfig ? this.createConfig.title : this.title);
       this.show();
       
       this.fireEvent('create',this);

       if (this.showKeepOpener){
           this.keepOpenedCheck.show();
           this.keepOpenedCheck.setValue(true);
       }else{
           this.keepOpenedCheck.hide();
       }
       
       this.syncSize();//to fix layout bug
   },

   /**
    * Open the form to edit a record
    */
   edit : function(record){
       var title = this.editConfig ? this.editConfig.title : this.title;

       if (record){
           this.show();
           this.syncSize();
           this.keepOpenedCheck.hide();

           this.record = record;
           
           if (this.editConfig){
               if (this.editConfig.titleField)
                  title += ' (' + record.data[this.editConfig.titleField] + ')';
           }
           
           this.setTitle(title);
           this.form.getForm().loadRecord(record);
           this.fireEvent('edit',this);

       }
   },

   /**
    * Build the form
    * @todo implements build form from form config
    * @private
    */
   createForm : function(){
      var form = {
          xtype: this.ftype,
          itemId: 'form',
          baseCls: 'x-plain',
          api:{
              submit: this.saveAction ? this.saveAction : this.module.directAction.save
          },
          listeners:{
              beforeaction: this.beforeSave,
              actioncomplete: this.afterSave,
              scope: this
          }
      }

      return form;
   },


   /**
    * Reset the form and close window
    */
   cancel : function(){
       try{
           if (!this.form.getForm().isDirty()){
               this.form.getForm().reset();
               this.hide();
               this.record = null;
               if (this.reloadOnHide){
                   this.module.dataList.load();
               }
           }else{
                Ext.Msg.show({
                   title: this.confirmCloseTitle,
                   msg: this.confirmCloseMsg,
                   buttons: Ext.Msg.YESNO,
                   scope: this,
                   fn: function(btn){
                       if (btn == 'yes'){
                           this.form.getForm().reset();
                           this.hide();
                           this.record = null;
                           if (this.reloadOnHide){
                               this.module.dataList.load();
                           }

                       }
                   },
                   icon: Ext.MessageBox.QUESTION
                });
           }

       }catch(e){
           this.form.getForm().reset();
           this.hide();
           this.module.dataList.load();

       }
       
   },

   /**
    * Save form data
    */
   executeSave : function(){
       if (this.form.getForm().isValid()){
           var params = {};
           if (this.record){
               params = {
                   id: this.record.id
               }
           }
           this.fireEvent('beforesave',params,this);
           this.form.getForm().submit({params:params});
       }else{
            Wow.Msg.flash({
                msg: this.invalidFormSaveMsg,
                type: 'error'
            });
       }
   },

   beforeSave : function(form, action){
      //show loader plugin in save button
      this.saveButton.plugins[0].show();
   },

   afterSave : function(form, action){
      //hide loader plugin in save button
      this.saveButton.plugins[0].hide();

      //show success message
      Wow.Msg.flash({
          msg: this.successSaveMsg,
          type: 'success'
      });

      //reset form
      this.form.getForm().reset();
      
      //if it is in edit module
      if (this.record){
          this.cancel();
          this.module.dataList.load();
      }else{
          if (!this.keepOpenedCheck.getValue()){
              this.cancel();
              this.module.dataList.load();
          }else
              this.reloadOnHide = true;
      }

  }


   
});

Ext.reg('w-dataform',Wow.form.DataForm);

