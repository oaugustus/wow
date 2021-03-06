/*
 * File: GroupForm.ui.js
 * Date: Tue Aug 17 2010 21:53:40 GMT-0300 (Hora oficial do Brasil)
 * 
 * This file was generated by Ext Designer version xds-1.0.2.11.
 * http://www.extjs.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

GroupFormUi = Ext.extend(Ext.form.FormPanel, {
    width: 400,
    height: 453,
    padding: 10,
    border: false,
    labelWidth: 70,
    initComponent: function() {
        this.items = [
            {
                xtype: 'textfield',
                fieldLabel: 'Nome',
                anchor: '100%',
                name: 'name',
                allowBlank: false
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Descrição',
                anchor: '100%',
                name: 'description'
            },
            {
                xtype: 'checkbox',
                fieldLabel: 'Label',
                boxLabel: 'Este grupo está ativo',
                anchor: '100%',
                hideLabel: true,
                name: 'active',
                ref: 'ckActive'
            },
            {
                xtype: 'treepanel',
                title: 'Permissões',
                height: 358,
                rootVisible: false,
                autoScroll: true,
                ref: 'treePermission',
                root: {
                    text: 'root',
                    expanded: true,
                    id: 'root'
                },
                loader: {
                    paramsAsHash: true,
                    requestMethod: 'POST'
                }
            },
            {
                xtype: 'hidden',
                fieldLabel: 'Label',
                anchor: '100%',
                name: 'id',
                ref: 'edtId'
            }
        ];
        GroupFormUi.superclass.initComponent.call(this);
    }
});
