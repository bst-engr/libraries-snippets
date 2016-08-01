/**
 * Created by:  Muhammad Basit Munir
 * Date:1 june 2016.
 * Description: contatins function related to default values for example available colors in system
 * and functions related to check for available colors in system.
 */
angular.module('MarkupModule',[]).factory('Markup', [function(){
	/**
	 * [Markup functions used to initialize variables]
	 */
    var Markup = function() {
    	/**
    	 * [availableColors list of available colors in system]
    	 * @type {Array}
    	 */
        this.availableColors = [
            {'color':'Black'},
            {'color':'Blue'},
            {'color':'Gray'},
            {'color':'Green'},
            {'color':'Orange'},
            {'color':'Pink'},
            {'color':'Purple'},
            {'color':'Red'},
            {'color':'White'},
            {'color':'Yellow'}
        ];
    };
    /**
     * [ifColorExists created to check if given (available color exists in response array.)]
     * @param  {string} element [element to find in object]
     * @param  {array} list    [array of objects]
     * @return {bool}         [return false if element exists else returns true to disable/enable field at dom]
     */
    Markup.prototype.ifColorExists = function (element, list) {

	    var i;
	    for (i = 0; i < list.length; i++) {
	    	if (list[i].color === element) {
	        	return false;
	        }
	    }
	    return true;
	
    }

    /**
     * [popArrayObject used to remove element from list of selected item if it is inactive on dom, takes an array of selected item and value to check as second parameter]
     * @param  {array} obj   [array of objects of selected  items]
     * @param  {int} value [internal id of the value to remove from selected items]
     * @return {array}       [Array of remaining objects]
     */
    Markup.prototype.popArrayObject = function (obj, value, vendorId) {
        for (var i = 0; i < obj.length; i++) {
            if(obj[i].internalID == value && obj[i].vendor_id == vendorId ) {
                obj[i].active = false;
                obj[i].addedToCart = false;
                //console.log(obj[i]);
                obj.splice(i, 1);
                //console.log(JSON.stringify(obj));
            }
        }
        return obj;
    }

    /**
     * [updateQuantity udpates quantity of selected vendor, updates quantity state and keep record of each item and vendors state]
     * @param  {array} obj    [array of objects of selected items]
     * @param  {object} vendor [vendor object which quantity need to be update to selected item's quantity]
     */
    Markup.prototype.updateQuantity = function (obj, vendor) {
        
        for (var i = 0; i < obj.length; i++) {
            if(obj[i].internalID == vendor.internalID && obj[i].vendor_id == vendor.vendor_id) {
                obj[i].quantity = vendor.quantity;
            }
        }
        return obj;   
    }

    // build the api and return it
    return Markup;

}]);
