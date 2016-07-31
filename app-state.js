/**
 * Created by:  Muhammad Basit Munir
 * Date: 9 june 2016.
 * Description: 
 */
angular.module('angularCustom',[]).factory('State', ['$http', function($http){
	/**
	 * [Custom functions used to initialize variables]
	 */
    var State = {
            includeStockedItem: false,
            site: 'fcm',
            showSpinner: false,
            selectedItems: [],
            cartItems:[],
            enableSendquote: false,
            showSendquote: false,
            enableClassWrap:false,
            enableSessionExpiry: false,
            campemail:'',
            savedQuote :{
                "site": "fcm",
                "sumcost":0.00,
                "sumcell":0.00,
                "extCost":0.00,
                "unitsell":0.00,
                "quote_number":0,
                "num_of_items":0,
                "customer_email":"",
                "contact_name":"",
                "customer_phone":"",
                "company_name":"",
                "customer_description":"",
                "chkHTML" : true

            }
        };
    
    /**
     * [addItemIntoCart unction used to push selected items to server side to save items into cart.]
     */
    State.addItemIntoCart = function ( priceOject ) {
	    $http.post(State.url('add-to-cart'),{action:'add-items',items: State.selectedItems}).then(
                function(response){
                    State.cartItems=response.data;
                   // console.log(State.cartItems);
                    State.handleSelectedITems();
                },
                function(response){
                    console.log('there is something wrong please review');
                }

            );
        
    };
    /**
     * [delItemIntoCart description]
     * @param  {[type]} itemID [description]
     * @return {[type]}        [description]
     */
    State.delItemIntoCart = function (itemID) {
        $http.post(State.url('add-to-cart'),{action:'del-items',itemID:itemID}).then(
                function(response){
                    State.cartItems=response.data;
                },
                function(response){
                    console.log('there is something wrong please review');
                }

            );
        
    };
    /**
     * [editItemIntoCart description]
     * @param  {[type]} itemID [description]
     * @param  {[type]} qty    [description]
     * @return {[type]}        [description]
     */
    State.editItemIntoCart = function (itemID,qty,pkitemID) {
        $http.post(State.url('add-to-cart'),{action:'edit-items',pkQuoteItemID:itemID,quantity:qty,pkItemID:pkitemID}).then(
                function(response){
                    State.cartItems=response.data;
                },
                function(response){
                    console.log('there is something wrong please review');
                }

            );
        
    };
    /**
     * [delAllItemsIntoCart description]
     * @return {[type]} [description]
     */
     State.delAllItemsIntoCart = function () {
        $http.post(State.url('add-to-cart'),{action:'del-AllItems'}).then(
                function(response){
                    State.cartItems=response.data;
                },
                function(response){
                    console.log('there is something wrong please review');
                }

            );
        
    };
    /**
     * [handleSelectedITems description]
     * @return {[type]} [description]
     */
    State.handleSelectedITems = function (vendor) { 

        top:
        for (var i = 0; i < State.cartItems.items.length; i++) {
            for(var j = 0; j < State.selectedItems.length; j++) {

                if(State.selectedItems[j].internalID == State.cartItems.items[i].itemID && State.selectedItems[j].vendor_id == State.cartItems.items[i].vendor_id ) {
                    State.selectedItems[j].addedToCart = true;
                    vendor = vendor || false;
                    if (vendor!=false) { 
                        vendor.addedToCart = true;
                    }
                    continue top;
                }
            }
            
        }
    };
    /**
     * [handleSelectedITems description]
     * @return {[type]} [description]
     */
    State.resetSelectedItems = function (price) { 
        price.items.filter(function(priceRow){
                priceRow.highlighted= false;
                priceRow.spinnerEnabled=false;
                priceRow.vendors.filter(function (v) {
                    v.active = false;
                    v.addedToCart = false;
                });
        });
    };
    /**
     * 
     */
    State.saveAppState = function () {
        localStorage.setItem('appState', JSON.stringify(State));
    }
    /**
     * [restoreAppState description]
     * @return {[type]} [description]
     */
    State.restoreAppState = function () {
        return JSON.parse(localStorage.getItem("appState"));
    };

    /**
     * [removeAppState description]
     * @return {[type]} [description]
     */
    State.removeAppState = function () {
        localStorage.removeItem('appState');
    };
    /**
     * getSaveQuoteData used to get data of saved quote to prepare it to send and push to netsuite.
     * @return
     */
    State.getSaveQuoteData = function () {
        $http.post(State.url('send-quote'),{action:'get_save_quote_data'}).then(
            function(response){
                $("body").hideLoader();

                State.savedQuote = response.data;//.data;
                console.log(State.savedQuote);
            },
            function(response){
                $("body").hideLoader();
                console.log('there is something wrong please review');
            }

        );
        
    }
    /**
     * [sendNewQuote description]
     * @return {[type]} [description]
     */
    State.sendNewQuote = function () {
        console.log("sendquote HTMl Checked? :"+State.savedQuote.sendquote_html);
        State.savedQuote.chkMArgin = State.savedQuote.chkMArgin === true ? 'yes' : 'no';
        State.savedQuote.chkText = State.savedQuote.chkText === true ? 'yes' : 'no';
        State.savedQuote.chkPDF = State.savedQuote.chkPDF === true ? 'yes' : 'no';
        State.savedQuote.chkHTML = State.savedQuote.chkHTML === true ? 'yes' : 'no';
        var data;
        $http({
        method: 'POST',
        url: 'generateQuote.php?action=sendquote',
        data: State.savedQuote,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(response){
            if(response == 'sent!') {
                alert('Sent!');
                State.savedQuote = {'chkHTML':true};
                State.enableSendquote=false;
                State.showSendquote=false;
                State.enableClassWrap=false;
            } else {
                State.savedQuote.chkMArgin = State.savedQuote.chkMArgin === 'yes' ? true : false;
                State.savedQuote.chkText = State.savedQuote.chkText === 'yes' ? true : false;
                State.savedQuote.chkPDF = State.savedQuote.chkPDF === 'yes' ? true : false;
                State.savedQuote.chkHTML = State.savedQuote.chkHTML === 'yes' ? true : false;
                alert(response);
            }
        });
    }

    /**
     * [pushQuote description]
     * @return {[type]} [description]
     */
    State.pushQuote = function () {
        return $http.post('pushQuote.php?action=pushquote', {'status': 'push', 'email' : State.campemail});
    }

    /**
     * [url generates a url for given page name]
     * @param  {string} page [page name to which request need to send]
     * @return {string}      [complete url to given page]
     */
    State.url = function (page) {
            console.log('url function called')
            return "/v4config/app/cat-cables/"+page+".php";
    };

    /**
     * [getCartData description]
     * @return {[type]} [description]
     */
    State.getCartData = function (){
        $("#patch_cables").showLoader();
        console.log("sending request for patch cables");
        $http.post( State.url("patch_cables"), {action:'load_cart'} ).then(
            function (response) { // success
               $("#patch_cables").hideLoader();
               State.cartItems = response.data;
                //scope.markup.handleSelectedITems(State.cartItems, scope.state.selectedItems);
            },
            function (response) { // error
                $("#patch_cables").hideLoader();
            }
        );
    }

    /**
     * [getCartData description]
     * @return {[type]} [description]
     */
    State.onIncludeStockedItem = function () {
        $("#patch_cables").showLoader();
        var chkd = State.includeStockedItem ? 'yes' : 'no';
        return $http.post( State.url("patch_cables"), {action:'include_stocked_item', site: State.site, chkd: chkd, itemID: '' } );
    }

    State.checkEmail = function (selectedItem) {
        //$email, $length, $cable, $priceFormulaValue, $site
        console.log("checkEmail function Called/ /State/");
        $(".content-right").showLoader();
        $http.post('/v4config/app/cat-cables/patch_cables.php', {'action': 'check_emailBackNew', 'email': selectedItem.email, site: State.site, length: '', priceFormulaValue: '', cable: ''}).then(
            function (response) { //success
                $(".content-right").hideLoader();
                console.log('retuned patch cable: check_emailBackNew');
                 State.getCartData();

            }, 
            function (error) {
                $("#patch_cables").hideLoader();
                console.log(error);
            }
        );
    }

    State.selectItem = function (selectedItem) {
        console.log("selectedItem called");
        $(".content-right").showLoader();
        $http.post('/v4config/app/cat-cables/patch_cables.php', {'action': 'check_ltn', 'email': selectedItem.email}).then(
            function (response) { //success
                $(".content-right").hideLoader();
                State.checkEmail(selectedItem);
            }, 
            function (error) {
                console.log(error);
                $("#patch_cables").hideLoader();
            }
        );
    }
    // build the api and return it
    return State;

}]);
