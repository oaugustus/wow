Ext.ux.form.ImageField = Ext.extend(Ext.form.FileUploadField, {
   noneImageSelectedText: 'Sem imagem para exibir',
   waitingSendText: 'Aguardando ação salvar...',
   initComponent : function(){

       Ext.applyIf(this,{
           position: 'top',
           imageHeight: 70,
           imagePad: 5
       });
       
       Ext.ux.form.ImageField.superclass.initComponent.apply(this, arguments);

       this.on('fileselected',function(){
          this.image.setVisible(false);
          this.imageTitle.setVisible(true);
          this.imageTitle.center(this.imageContainer);
          this.imageTitle.setY(this.imageTitle.getY() -15);
          this.imageTitle.update(this.waitingSendText)

       },this);
   },

   afterRender : function(){
       Ext.ux.form.ImageField.superclass.afterRender.apply(this,arguments);

       var ctDesc = {
           cls: 'image-field-container',
           style: 'margin-bottom: ' + this.imagePad + 'px;',
           children:[
               {
                  tag:'table',
                  children: [
                      {
                          tag: 'tr',
                          children:[
                              {
                                  tag: 'td',
                                  align: 'center',
                                  children:[
                                      {tag: 'div',html: this.noneImageSelectedText, id: this.id + '-imagetitle', cls: 'title'},
                                      {tag: 'img', height: this.imageHeight - 10, align: 'center', id: this.id + '-image', style: 'display:none'}
                                  ]
                              }
                          ]
                      }
                  ]
               }
           ]
       }
       
       if (this.position == 'top'){
           this.imageContainer = Ext.get(Ext.DomHelper.insertBefore(this.label.parent(),ctDesc));
       }else{
           this.imageContainer = Ext.get(Ext.DomHelper.insertAfter(this.label.parent(),ctDesc));
       }

       this.image = Ext.get(this.id + '-image');
       this.imageTitle = Ext.get(this.id + '-imagetitle');
       
       this.imageContainer.setWidth(this.lastSize.width);
       this.imageContainer.setHeight(this.imageHeight);

   },

    setValue : function(v){
        if (!this.rendered){
            this.on('afterrender',function(){
                this.setValue(v);
            },this);
            return;
        }
        
        Ext.ux.form.ImageField.superclass.setValue.apply(this, arguments);

        if (this.fileInput.dom.value == '')
            this.setImage(v);
    },

    setImage : function(v){
        this.image.dom.src = Ext.app.UPLOAD_URL + v;
        this.image.setVisible(true);
        this.image.center(this.imageContainer);
        this.imageTitle.setVisible(false);
    },

    reset : function(){
        Ext.ux.form.ImageField.superclass.reset.apply(this, arguments);
        this.image.setVisible(false);
        this.imageTitle.center(this.imageContainer);
        this.imageTitle.setY(this.imageTitle.getY() -15);
        this.imageTitle.setVisible(true);
    }


});

Ext.reg('imagefield',Ext.ux.form.ImageField);

