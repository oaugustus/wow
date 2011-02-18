// vim: ts=4:sw=4:nu:fdc=2:nospell
/**
 * Ext.ux.util.renderer - an extensible
 *
 * @author  Otávio Augusto Rodrigues Fernandes
 * @date    8. July 2010
 *
 *
 * @license Ext.ux.Util is licensed under the terms of
 * the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 * that the code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 *
 * License details: http://www.gnu.org/licenses/lgpl.html
 */

Ext.ns('Ext.ux.util');
/**
 * @class Ext.ux.util.renderer
 *
 */
Ext.ux.util.renderer = function(){
    return {
        moneyFormat : function(num){
          x = '';
          if (num){
              sep = ".";
              decpoint = ",";
              // need a string for operations
              num = num.toString();
              // separate the whole number and the fraction if possible
              a = num.split('.');
              x = a[0]; // decimal
              y = a[1]; // fraction
              z = "";

              if (typeof(x) != "undefined") {
                // reverse the digits. regexp works from left to right.
                for (i=x.length-1;i>=0;i--)
                  z += x.charAt(i);
                // add seperators. but undo the trailing one, if there
                z = z.replace(/(\d{3})/g, "$1" + sep);
                if (z.slice(-sep.length) == sep)
                  z = z.slice(0, -sep.length);
                x = "";
                // reverse again to get back the number
                for (i=z.length-1;i>=0;i--)
                  x += z.charAt(i);
                // add the fraction back in, if it was there
                if (typeof(y) != "undefined" && y.length > 0)
                  x += decpoint + y;
              }

          }

          return x;
            
        }
    }
}();



