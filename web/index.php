<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
  // include the config file
  include "config.php";

  // include the scripts autload class
  include "autoload/jsload.php";
?>

<html>
  <head>
    <title><?php echo SYSTEM_NAME?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="lib/extjs/resources/css/ext-all.css">
    <link rel="stylesheet" type="text/css" href="lib/extjs/ux/css/xcheckbox.css">
    <link rel="stylesheet" type="text/css" href="lib/extjs/ux/css/CheckTreePanel.css">
    <link rel="stylesheet" type="text/css" href="lib/extjs/ux/css/RowEditor.css">
    <link rel="stylesheet" type="text/css" href="lib/extjs/ux/css/file-upload.css">
    <link rel="stylesheet" type="text/css" href="lib/extjs/ux/css/ImageField.css">

    <!-- Wow-->
    <link rel="stylesheet" type="text/css" href="lib/wow/resources/css/actions.css"/>
    <link rel="stylesheet" type="text/css" href="lib/wow/resources/css/msg.css"/>
    <link rel="stylesheet" type="text/css" href="lib/wow/resources/css/search.css"/>
    <link rel="stylesheet" type="text/css" href="lib/wow/resources/css/gridfilters.css"/>
    <link rel="stylesheet" type="text/css" href="lib/wow/resources/css/rangemenu.css"/>
    <link rel="stylesheet" type="text/css" href="css/system.css"/>
    <link rel="stylesheet" type="text/css" href="css/templates.css"/>

  </head>
  <body>
    <script type="text/javascript" src="lib/extjs/adapter/ext/ext-base-debug.js"></script>
    <script type="text/javascript" src="lib/extjs/ext-all-debug.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/ext.ux.util.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/ext.ux.form.xcheckbox.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/ext.ux.form.xdatefield.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/ext.ux.form.xcombobox.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/RowEditor.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/ext.ux.form.xgridfield.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/OneToManyField.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/Ext.ux.renderer.combo.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/Ext.ux.renderer.xcombo.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/ext.ux.form.xcheckgrid.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/CheckTreePanel.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/CheckColumn.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/ManyToManyField.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/MoneyField.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/textmask.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/fileupload/FileUploadField.js"></script>
    <script type="text/javascript" src="lib/extjs/ux/ImageField.js"></script>
    <script type="text/javascript" src="<?php echo BASE_URL.INDEX_PAGE?>direct/api"></script>
    <script type="text/javascript">
      Ext.app.SYSTEM_NAME = '<?php echo SYSTEM_NAME ?>';
      Ext.app.URL = '<?php echo BASE_URL?>';
      Ext.app.UPLOAD_URL = '<?php echo UPLOAD_URL?>';
      Ext.app.REPORT_URL = '<?php echo REPORT_URL?>';
      Ext.BLANK_IMAGE_URL = 'lib/extjs/resources/images/default/s.gif';
    </script>
    <!-- Wow - @todo generating minified-->
    <!-- Msg-->
    <script type="text/javascript" src="lib/wow/msg/Msg.js"></script>
    <!-- Mask-->
    <script type="text/javascript" src="lib/wow/mask/Mask.js"></script>
    <!-- List-->
    <script type="text/javascript" src="lib/wow/list/adapter/GridPanel.js"></script>
    <script type="text/javascript" src="lib/wow/list/adapter/EditorGridPanel.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/menu/ListMenu.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/menu/RangeMenu.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/filters/Filter.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/filters/StringFilter.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/filters/NumericFilter.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/filters/FloatFilter.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/filters/BooleanFilter.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/filters/ListFilter.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/filters/DateFilter.js"></script>
    <script type="text/javascript" src="lib/wow/list/search/Search.js"></script>
    <script type="text/javascript" src="lib/wow/list/DataList.js"></script>
    <!-- Form-->
    <script type="text/javascript" src="lib/wow/form/DataForm.js"></script>
    <!-- Data-->
    <script type="text/javascript" src="lib/wow/data/Provider.js"></script>
    <!-- Auth-->
    <script type="text/javascript" src="lib/wow/auth/Auth.js"></script>
    <script type="text/javascript" src="lib/wow/auth/Login.ui.js"></script>
    <!-- Module-->
    <script type="text/javascript" src="lib/wow/module/Module.js"></script>
    <script type="text/javascript" src="lib/wow/module/DetailModule.js"></script>
    <script type="text/javascript" src="lib/wow/workspace/ModuleProvider.js"></script>
    <script type="text/javascript" src="lib/wow/workspace/Application.js"></script>
    <script type="text/javascript" src="lib/wow/workspace/themes/office/Desktop.js"></script>
    <script type="text/javascript" src="lib/wow/workspace/themes/office/Dashboard.js"></script>
    <script type="text/javascript" src="lib/wow/workspace/themes/office/Menu.js"></script>
    <script type="text/javascript" src="lib/wow/workspace/themes/office/MenuGroup.js"></script>
    <script type="text/javascript" src="lib/wow/exception.js"></script>
    <script type="text/javascript" src="lib/wow/config.js"></script>
    <script type="text/javascript" src="lib/extjs/overrides/override.js"></script>
    
    <!-- Locale script -->
    <script type="text/javascript" src="lib/locale/pt_br.js"></script>

    <!-- Load application scripts  -->
    <?php JSLoad::load();?>
  </body>
</html>