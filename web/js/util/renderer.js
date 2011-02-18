Ext.ns('Ext.app.util');

// singleton renderer class
Ext.app.util.renderer = function(){
    return {
        // return active status
        renderActive : function(v){
            switch(v){
                case '1':
                case 1:
                case true:
                    return Wow.locale.ActiveText;
                break;
                case '0':
                case 0:
                case false:
                    return Wow.locale.InactiveText;
                break;
            }
        },

        // return yes/no options
        renderYesNo : function(v){
            switch(v){
                case '1':
                case 1:
                case true:
                    return Wow.locale.YesText;
                break;
                case '0':
                case 0:
                case false:
                    return Wow.locale.NoText;
                break;
            }
        }
    }
}();

