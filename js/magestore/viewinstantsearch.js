var ViewInstantsearch = Class.create();
var ViewInstantsearch = Class.create();
var idInput = '';
ViewInstantsearch.prototype = {
    initialize: function(searchUrl,loadProductUrl,maxThumb, idInput){
		this.idInput = idInput;
        this.searchUrl = searchUrl;	
		this.loadProductUrl = loadProductUrl;			
		this.onSuccess = this.onSuccess.bindAsEventListener(this);        
		this.onFailure = this.onFailure.bindAsEventListener(this);				
		this.currentSearch = '';
	    this.currentSuggestion = '';
	    this.searchWorking = false;
		this.loadWorking = false;
		this.loadPending = false; 
		this.pendingProductId = '';
		this.pendingMoreProductNum = '';
	    this.searchPending = false; 
	    this.moreProductsShowing = false; 	    
	    this.currentMoreProductNum = 0;
		this.maxThumb = maxThumb;	
    },
	
	search: function(){	
		 this.currentMoreProductNum = 0; 
		if (this.searchWorking) {
	        this.searchPending = true;
	        return;
		}
		var searchBox = $(this.idInput);
	    
		if(searchBox.value=='')
		{
			this.updateSuggestedKeyword("Search Product Instantly");
			return;
		}
		
	    if ((this.currentSearch!="") &&(searchBox.value == this.currentSearch)) {
	        return;
	    }
	    this.currentSearch = searchBox.value;
		
		searchBox.className =  'statusLoading input-text';
		var keyword = searchBox.value;
		
		url = this.searchUrl;
		
		var parameters = {keyword: keyword};
		
		new Ajax.Request(url, {
			  method: 'post',		 
			  parameters: parameters,
		      onSuccess: this.onSuccess,
			  onFailure: this.onFeailure 
		  });
		  
		 this.searchWorking = true;  
    },

	onFailure: function(transport){
        $(this.idInput).className ="";
    },
	
	
	onSuccess: function(transport)
	{
		var searchBox = $(this.idInput);
		if (transport && transport.responseText) {
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
			
			if (response.products) {
				this.updateSuggestedKeyword("Search Product Instantly");				
				this.updateProductDisplay(response.products);
				
				searchBox.className = 'statusPlaying input-text';
            }
			else
			{
				this.updateSuggestedKeyword('No results for "'+this.currentSearch+'"');
				searchBox.className ="input-text";
			}			
		}
		
		 this.doneWorking();		
	},
	
	updateProductDisplay: function(products) {
	    var numThumbs = (products.length >= this.maxThumb) ? this.maxThumb : products.length;

	    var moreProducts =  new Element('div', { 'id': 'moreViewProducts'});
		
		
		
	    for (var i = 0; i < numThumbs; i++) {
	        var productId = products[i].id;
	        // Load the other videos' thumbnails
	        
			var img = new Element('img', { "src": products[i].image, "id":"viewProduct"+(i+1)});
			var a = new Element('a', {"title":products[i].name, "href": "javascript:viewInstantsearch.loadProductDetail('"+productId+"', "+(i+1)+")" });
	        
	        a.appendChild(img);
	        moreProducts.appendChild(a);
	    }
	    var moreProductsWrapper = $('moreViewProductsWapper');

	    $('moreViewProducts').remove();
	    moreProductsWrapper.appendChild(moreProducts);

	    if (!this.moreProductsShowing) {
	        
	        this.moreProductsShowing = true;
	    }
		
		this.loadProductDetail(products[0].id,1);
	},
	
	doneWorking: function() {
	    this.searchWorking = false;

	    if (this.searchPending) {
	        // another search happened while we were processing this one, so we need to take care of it.
	        this.searchPending = false;
	        this.search();
	    }	    
	},
	
	doneLoadingProduct: function() {
	    
		this.loadWorking = false;
		
	    if (this.loadPending) {			
	        this.loadPending = false;			
	        this.loadProductDetail(this.pendingProductId,this.pendingMoreProductNum);
	    }
	    
	},
	
    updateSuggestedKeyword: function(message)
	{
		$("searchTermkeyword").update(message);
	},
	
	
	loadProductDetail: function(productId,moreProductNum){			
		if (this.loadWorking) {	  					
			this.loadPending = true;	        
			this.pendingProductId  = productId;	
			this.pendingMoreProductNum = moreProductNum;	
			return;
		}
		
		if (this.currentMoreProductNum == moreProductNum) {	        
	        return;
	    }		
		
		var currentMoreProductNum = this.currentMoreProductNum;
		
		if(currentMoreProductNum)
		{
			$("viewProduct" + currentMoreProductNum).className = "";
		}	
		$("viewProduct" + moreProductNum).className = "active";		
				
		this.currentMoreProductNum = moreProductNum;
		
		
		var url = this.loadProductUrl+"id/" + productId;
		$("mainViewProduct").className = "productLoading";
		new Ajax.Updater("mainViewProductWapper", url, {method: 'get', onFailure: "",onComplete:this.doneLoadingProduct.bindAsEventListener(this)}); 
		this.loadWorking = true; 	
    }
	
}





   

