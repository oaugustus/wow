/**
 * Exception handling
 */
Ext.Direct.on('exception',function(e){
    // if any web kit is running on browser
    try{
        // show a debug message
        console.debug(e);
    }catch(e){}
    
	// show a flash message
    Wow.Msg.flash({
        msg: Wow.locale.exception.InfoMessage + e.message,
        type: Wow.exception.InfoType,
        pause: Wow.exception.InfoTime
    });

    // if there are any load maks target
    if (Ext.app.maskTarget){
        // hide the mask
        Ext.app.maskTarget.hide();
    }
});


