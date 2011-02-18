Ext.ux.form.ImageUploadField = Ext.extend(Ext.form.TextField, {
   noneImageSelectedTitle: 'Nenhuma imagem selecionada',
   noneImageSelectedText: 'Clique dentro da área tracejada para selecionar uma imagem',
   removeUploadIcon: 'remove-upload-icon',
   removeUploadTip: 'Remover este arquivo',
   initComponent : function(){
       
       Ext.ux.form.ImageUploadField.superclass.initComponent.apply(this,arguments);

       this.addEvents(
            /**
             * @event fileselected
             * Fires when the underlying file input field's value has changed from the user
             * selecting a new file from the system file selection dialog.
             * @param {Ext.form.FileUploadField} this
             * @param {String} value The file value returned by the underlying file input field
             */
            'fileselected'
       );
   },

   onRender : function(ct, position){
       Ext.ux.form.ImageUploadField.superclass.onRender.apply(this, arguments);

       this.ct = ct;
       this.position = position;

       this.wrap = this.el.wrap({cls:'x-form-field-wrap x-form-file-wrap'});
       this.el.addClass('x-form-file-text');
       this.el.dom.removeAttribute('name');

       this.imageLabel = this.wrap.createChild({
           innerHTML : 'Teste'
       });

       this.fileInput = this.wrap.createChild({
            id: this.getFileInputId(),
            name: this.name||this.getId(),
            cls: 'x-form-file',
            tag: 'input',
            type: 'file',
            size: 1
       });

       this.wrap.setWidth(this.width ? this.width : this.height);
       this.wrap.setHeight(this.height ? this.height : this.width);

       Ext.get(this.getFileInputId()).setHeight(this.height ? this.height : this.width);

        var btnCfg = Ext.applyIf(this.buttonCfg || {}, {
            text: this.buttonText
        });


        this.removeButton = new Ext.Button(Ext.apply(btnCfg, {
            renderTo: this.wrap,
            iconCls: this.removeUploadIcon,
            tooltip: this.removeUploadTip
        }));

       this.wrap.on('mousemove',function(e, t, o){
           Ext.get(this.getFileInputId()).setX(e.getPageX() - 70);
       },this);

       this.tTip = new Ext.ToolTip({
            target: this.getFileInputId(),
            title: this.noneImageSelectedTitle,
            width:200,
            html: this.noneImageSelectedText,
            trackMouse:true
        });
        
       this.el.hide();
   },

    // private
    getFileInputId: function(){
        return this.id+'-file';
    }

});

Ext.reg('imageuploadfield',Ext.ux.form.ImageUploadField);

