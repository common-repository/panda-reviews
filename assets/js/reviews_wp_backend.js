function reviewsWpBackend(){

    var $ = jQuery,
        self = this,
		manageReviewsObj = new manage_reviews();

    this.init = function(){		

		manageReviewsObj.init();
		manageReviewsObj.check_all();

		$('.colorpick').colorPicker();

		self.submit_general_settings_form();
    }

    this.saveOptionsAjax = function(data, callback){

        $.ajax({
            type:"POST",
            url:pxreviews.ajax_url,
            data:{
                "action":pxreviews.action,
                "section":"save_options",
                "data":data
            },
            success:function(data){

                callback( data );

            },

            dataType:"html"
        })

    };

	this.submit_general_settings_form = function(){

		var form = document.forms["pxgeneral-settings-form"];       

		if ( 'undefined' === typeof form || null === form ) return false;

		 if( form.addEventListener ){
            
			form.addEventListener("submit", function(event){
                $(".px-saved-msg").hide();
				$("#px-btn-loading").show();
				
				event.preventDefault();
				var dataForm = form.elements;
				var data = self.serializeFields( dataForm );

				// save general settings
				self.saveOptionsAjax( data, function(data){
					$("#px-btn-loading").hide();
					$(".px-saved-msg").show();
				});
                    
            }, false);

        } else if(form.attachEvent){
            
			form.attachEvent("onsubmit", function(event){
				
				$("#px-btn-loading").show();
				$("#px-btn-loading").show();
				
				event.preventDefault();
				var dataForm = form.elements;
				var data = self.serializeFields( dataForm );
                
				// save general settings
				self.saveOptionsAjax( data, function(data){
					$("#px-btn-loading").hide();
					$(".px-saved-msg").show();
				});
            })
        }
	}

	this.serializeFields = function(data){

		var formData = new Object();

		for ( i = 0; i < data.length; i++ ) {
            
			var name = data[i].name,
                value = data[i].value;                        
            
			switch(data[i].name){
                
				case 'display-voting-system':
				case 'display-review-system':
				case 'display-classic-stars':
                    
					if ( data[i].checked ) {
						formData[name] = value;
					}

                	break;
                
				case 'px_reviews_post_types':
					
					if ( data[i].checked ) {
						
						if ( typeof formData[name] === 'undefined' ) {
							formData[name] = [];	
						}

						formData[name].push(value);
					}

					break;

				case 'scheme-type':
					
					if ( data[i].checked ) {
                    	formData[name] = value;
					}

                	break;

				case 'main-color':
					
					formData[name] = value;
					
					break;
            }
        }
        return formData;	

	}

    

}

jQuery(document).ready(function(){

    var pxReviewsMain = new reviewsWpBackend();

    pxReviewsMain.init();

});

function manage_reviews() {
	var $ = jQuery,
		_this = this;

	this.init = function(){
		$('#manage-reviews-headline .pxselect-options li').click(function(){
			var rel = $(this).attr('rel');
			
			switch (rel){
				
				case 'read':
					
					var checkedIDs = _this.serializeIDs();
					
					if( checkedIDs.length > 0 ) {
						_this.mark_as_read( checkedIDs, function(data){
							location.reload();
						})
					}

				break;
				
				case 'delete':

					var checkedIDs = _this.serializeIDs();

					if( checkedIDs.length > 0 ) {
						var confirmation = confirm( "Are you sure want to remove the checked reviews?" );
						if ( true === confirmation){
							_this.remove_reviews( checkedIDs, function(data){
								location.reload();
							})
						}
						
					}

				break;
			}
		})
	}
	
	this.check_all = function(){
		$('#check-all-reviews').on('click', function(){
			var checked = (  $(this).attr('checked') !== 'checked' ? 1 : 0 );
			if( !checked ){
				$('.px-checkbox').attr({'checked':'checked'});
				
			}
			else{
				$('.px-checkbox').removeAttr('checked');
			}
		})
	};

	this.mark_as_read = function(data, callback){
		
		$.ajax({
            type:"POST",
            url:pxreviews.ajax_url,
            data:{
                "action":pxreviews.action,
                "section":"mark_reviews_as_read",
                "data":data
            },
            success:function(data){

                callback( data );

            },

            dataType:"json"
        })
	};

	this.remove_reviews = function(data, callback){
		
		$.ajax({
            type:"POST",
            url:pxreviews.ajax_url,
            data:{
                "action":pxreviews.action,
                "section":"remove_reviews",
                "data":data
            },
            success:function(data){

                callback( data );

            },

            dataType:"json"
        })
	}

	this.serializeIDs = function(){
		var IDs = [];
		$('.pxreview-checkbox').each(function(){
			if( $(this).attr('checked') == 'checked' ) {
				IDs.push($(this).val());
			}
		});
		return IDs;
	}
}