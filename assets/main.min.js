jQuery(document).ready(function(){
    
    // @pxReviewsData = object loaded by PLugin
    // stop executing if it\'s not defined
    if( typeof pxReviewsData === 'undefined' ) return; 
    
    var pxReviews = new pxPostReview();
    pxReviews.initResponsive();
    pxReviews.init();

    POSTS.prototype.rating = new postRating();
    // load  post functionalities
    var postObject = new POSTS();
    
    // stars rating 
    postObject.rating.initRating();    
    postObject.blockPosts_hover();

    
});

var pxFormErrorsMsgs = {

    emailIsNotValid :   'Please add a valid email address.',
    emptyReview :       'There is no any review.',
    emptyName:          '"Name" field can\'t be empty'

};

function pxPostReview(){

    var self = this,
        $ = jQuery;
    
	this.obj_act = {
		'reviewAlreadySubmited': false
	};

    this.init = function(){

        self.textareaFocus();
        self.initReplyAction();

        $("#px-review-close-lightbox").click(function(){

            $("#px-review-post-review-lightbox").fadeOut();
            
        });

		$('.reviewsCount').click(function(){

			var scrollOffset = $('.px-reviews-replies-list').offset().top;

			$('html, body').animate({
				scrollTop:Math.round( scrollOffset - 50 )
			}, 400);

		})		
		


    }
    
    // Submit a review or a reply
    this.initSubmitCommet = function(){
        
        $("#px-submit-review").unbind("click");    
        $("#px-submit-review").bind("click", function(){

            var data = self.validateForm();

            if ( data !== false && false === self.obj_act.reviewAlreadySubmited ) {

				self.obj_act.reviewAlreadySubmited = true;

                var commentData = {};
                    commentData.type = $("#px-review-form-comment-type").val();
                    commentData.parentID = $("#px-review-form-comment-parent-id").val();
                
                self.submitReview( data, commentData, function(data){
                    
                    //callback
                    switch( commentData.type ){

                        case "review":
                                
                                var reviewsCount = parseInt( $("#px-reviews-count").text() );
                                var votingStars = '';

                                for( var i = 0; i < 5; i++ ) {
                                    votingStars += ( i < data.user_vote ? '<li class="active"></li>' : '<li></li>' );
                                }

                                var ratingTemplate = '<ul class="rating px-rating '+pxReviewsData.scheme_class+' "><li><ul class="rating-list">'+votingStars+'</ul></li></ul></li></ul>';
                                var headlineTemplate = '<div class="review-headline"><strong>'+data.name+'</strong> '+ratingTemplate+' <label class="date">'+data.date+'</label></div>';

                                var reviewTemplate = '<div class="px-user-review" data-id="'+data.last_inserted_id+'">'+headlineTemplate+'<div class="review-body">'+data.comment+'</div><div class="px-replies"><button class="px-submit-reply" type="button">Reply</button><div class="px-replies-wrapper no-replies"></div></div></div>';

                                $(".px-reviews-container").prepend(reviewTemplate);
								$(".px-reviews-replies-list").removeClass('inactive');
                                $("#px-reviews-count").text( reviewsCount+1 );
                                self.initReplyAction();
								
								var scrollOffset = $('.px-reviews-replies-list').offset().top;
								$('html, body').animate({
									scrollTop:Math.round( scrollOffset - 50 )
								}, 400)

                            break;
                        
                        case "reply":
                                
                                $(".px-user-review").each(function(){
                                    
                                    var ID = $(this).data("id");

                                    if( ID == commentData.parentID ) {

                                        var repliesCount = $(this).find(".px-reply").length;

                                        var className = ( repliesCount > 0 ? '' : 'last-reply' );

                                        var template = '<div class="px-reply '+className+'"><label class="username"><span>'+data.name+'</span></label><label class="date">'+data.date+'</label>'+data.comment+'</div>';
                                        
                                        $(this).find(".px-replies-wrapper").removeClass('no-replies');
                                        $(this).find(".px-replies-wrapper").prepend(template);

                                    }
                                });


                            break;

                    }

                    $("#px-review-post-review-lightbox").fadeOut(function(){
						self.obj_act.reviewAlreadySubmited = false;	
					});

                })

            }

        });

    }
    
    // init Replies action
    this.initReplyAction = function(){
        
         $(".px-submit-reply").unbind("click");
         $(".px-submit-reply").each(function(){

            $(this).bind("click", function(){
                
                var parent = $(this).closest(".px-user-review"); 
                var parentID = parent.data("id");
				var width = $("#px-review-post-review-lightbox").width();
				var leftOffset = Math.round( $(this).offset().left - width - 20 );
                var topOffset = $(this).offset().top+20;


                var dataArgs = {
                    title:'Leave a reply',
                    button_title:'Comment',
                    comment_placeholder:'Comment',
                    type:'reply',
                    parent_id:parentID,
                    offsetTop:topOffset,
                    offsetLeft:Math.abs(leftOffset)

                };
                
                self.showLightBox( dataArgs );
            })

        });
    }

    // @data = {
    //   @title:"Lightbox headline title, can include html tags", 
    //   @button_title:"button text",
    //   @type:review|reply
    //   @comment_placeholder: place holder for textarea field 
    //   @offsetTop,
    //   @offsetLeft
    // }
    // 
    this.showLightBox = function( data ){   
		
        var type = ( typeof data.type !== 'undefined' ? data.type : 'review' );
        var parentID = ( typeof data.parent_id !== 'undefined' ? data.parent_id : 0 );
        var lightboxWidth = $("#px-review-post-review-lightbox").width();

        var leftOffset = Math.abs( data.offsetLeft ) + 70;
        
        $(".review-headline-title label").html(data.title);
        $(".review-headline-title span").html('');
        $("#px-review-comment").text(data.comment_placeholder);
        $("#px-review-comment").attr( "data-placeholder", data.comment_placeholder );
        $("#px-submit-review").text(data.button_title);

        $("#px-review-form-comment-type").val(type);
        $("#px-review-form-comment-parent-id").val(parentID);

        $("#px-review-post-review-lightbox").css({left:leftOffset+"px", top:data.offsetTop+"px"});

        $("#px-review-post-review-lightbox").fadeIn();

        self.initSubmitCommet();
        

    }

    this.initResponsive = function(){
        
        // self.initPostReviewBox();
        $(window).resize(function(){
            $(window).bind("resize.postreview", self.initPostReviewBox() );
        });
        
    }



    this.initPostReviewBox = function(){

        $(window).unbind("resize.postreview");
        
        var width = $("#px-review-post-review-lightbox").width();
        var topOffset = Math.round( $(".px-rating").eq(0).offset().top );
        var leftOffset = Math.round( $(".px-rating").eq(0).offset().left - width - 20 );

        $("#px-review-post-review-lightbox").css({left:leftOffset+"px", top:topOffset+"px"});
    }

    this.textareaFocus = function(){

        var placeholder = $("#px-review-comment").data("placeholder");
        
        $("#px-review-comment").focus(function(){

            if ( $("#px-review-comment").val().toLowerCase() == placeholder.toLowerCase() ){
                
                $("#px-review-comment").val('');
            }    
            
            

        });

        $("#px-review-comment").blur(function(){
            
            if ( $(this).val() === '' ) {
                
                $("#px-review-comment").val( placeholder );

            }

        })

    }

    this.validateForm = function(){

        var formData = document.getElementById('px-review-form').elements;
        var serializedData = [];
        
        for( var elemKey in formData ){

            var fieldData = {};

            if ( isInt( elemKey ) && formData[elemKey].name !== '' ) {

                var formField = formData[elemKey]; 

                switch( formField.tagName.toLowerCase() ) {

                    case "textarea":

                            var value  = formField.value;
                            var dataAttr = formField.dataset;
                            var errorMsg = pxFormErrorsMsgs.emptyReview;

                            if ( value === '' ) {

                                $("#px-review-error-msg").text(errorMsg);

                                 return false;
                            }
                            
                            if ( typeof dataAttr.placeholder !== 'undefined' ) {

                                var placeholder = dataAttr.placeholder;

                                if ( placeholder.toLowerCase() == value.toLowerCase() ) {

                                    $("#px-review-error-msg").text(errorMsg);

                                    return false;

                                }

                            }

                        
                        break;

                    case "input":

                            if ( formField.type == "text" ) {

                                var dataAttr = formField.dataset;

                                if ( typeof dataAttr.validate !== 'undefined' ) {

                                    switch ( dataAttr.validate ) {

                                        case "email":

                                            var value = formField.value;

                                            if ( !isEmail( value ) ) {

                                                var errorMsg = pxFormErrorsMsgs.emailIsNotValid;

                                                $("#px-review-error-msg").text(errorMsg);

                                                return false;

                                            }    


                                            break;

                                        case "not-empty":

                                            var value = formField.value,
                                                errorMsg = pxFormErrorsMsgs.emptyName;
                                            
                                            if ( value === '' ) {

                                                $("#px-review-error-msg").text(errorMsg);

                                                return false;
                                            }

                                            break;

                                    }

                                } 

                            }
                        
                        break;

                }

                fieldData.name = formField.name;
                fieldData.value = formField.value;
                fieldData.type = formField.type;

                serializedData.push( fieldData );

            }

        }

        $("#px-review-error-msg").text('');
        
        return serializedData;
    }


    this.submitReview = function(data, commentData, callback){

        var postID = pxReviewsData.postID;

        $.ajax({
            type:"POST",
            url:pxreviews.ajax_url,
            data:{
                action:pxreviews.action,
                section:"submit_review",
                data:data,
                postID:postID,
                type:commentData.type,
                parent:commentData.parentID
            },
            success:function( data ){
                callback( data );
            },
            dataType:"json"

        })

    }

}

// POST RATING STARS 
function postRating(){
    
    var self = this,
        $ = jQuery;
    
    this.reviewsData = pxReviewsData;
    
    this.voted = 0;

    this.initRating = function(){
        
        // make Post Rating
        var rating = pxReviewsData.rating;

        if ( rating != 0 ) {

            self.changeActiveClassByIndex( $(".rating-list.rated"), rating );
            
        }

        // if user voted; make User Rating Vote
        if ( self.reviewsData.voted !== 0 ) {

            self.changeActiveClassByIndex( $(".rating-list.active"), self.reviewsData.voted );

        }
		
        countDigits(self.reviewsData.votes_count, jQuery("#px-reviews-votes-count") );
        
        // Vote Post
        self.vote( function( data ){
            // callback

            // retrieve the offsets for Review Lightbox
            var width = $("#px-review-post-review-lightbox").width();
            var topOffset = Math.round( $(".px-rating").eq(1).offset().top+20 );
            var leftOffset = Math.round( $(".px-rating").eq(1).offset().left - width - 20 );
            
            var rating = parseInt( data.votes_amount ) / parseInt( data.votes_count ); 
            // make post rating, after user's vote
            self.changeActiveClassByIndex( $(".rating-list.rated"), rating );

            var postReviewObject = new pxPostReview(),
                dataArgs = {
                    title:'Thank you! <strong>Do you want to submit a review?</strong>',
                    button_title:'Post a review',
                    comment_placeholder:'Post a review',
                    type:'review',
                    offsetTop:topOffset,
                    offsetLeft:Math.abs( leftOffset )

                };
            if ( pxReviewsData.show_reviews == 1 ) {
				// display review lightbox
            	postReviewObject.showLightBox( dataArgs );
			}
            

            $("#px-rating-voted-message").html("Rated");
            $("#px-reviews-votes-count").html( (parseInt( self.reviewsData.votes_count ) + 1 ) );

        });

        

    }

    // change active Rating Stars
    this.changeActiveClassByIndex = function(list, index){
		for( var i = 4; i >= index; i-- ) {
			list.children("li").eq(i).removeClass("active");
		}
        

        for ( var i = 0; i < index; i++ ) {

			if ( (i + 1) > parseFloat(index) && (i - 0.9) < parseFloat(index) ) {
				list.children("li").eq(i).addClass("half-active");	
			} else {
				list.children("li").eq(i).addClass("active");
			}
            
        }
    };
    
    // get number of active Rating Stars
    this.getListActiveRankingValue = function(list){
        return list.children("li.active").length;
    };
    
    // Stars Vote
    this.vote = function( callback ){
        
        $(function(){
            
            if ( pxReviewsData.userHasVoted == 1 ) return;

            $(".rating-list.active").each(function(){
                
                var _thisList = $(this);
                
                $(this).children("li").each(function(index){
                    
                    var  defaultRating = self.getListActiveRankingValue(_thisList);
                    
                    $(this).click(function(){
                        
                        var userRating = index+1;  
                        // defaultRating = userRating;                              
                        self.changeActiveClassByIndex(_thisList, userRating);        
                        
                        _thisList.addClass("inactive");

                        //ajaxRequest
                        self.updatePostRating_viaAjaxRequest( userRating, callback );
                    });

                });

                self.hover();

            });    
        })
        
    };
    
    // Stars Rating hover
    this.hover = function(){
        
        $(".rating-list.active").each(function(){
            
            var _thisList = $(this);
			
			$(this).hover(
				function(){},
				function(){
					if( _thisList.hasClass('inactive') ) return;
					self.changeActiveClassByIndex($(this), 0);
				}
			);

            $(this).children("li").each(function(index){
            
                $(this).hover(function(){
                    
                    if( _thisList.hasClass('inactive') ) return;
                    
                        self.changeActiveClassByIndex(_thisList, (index+1) );
                    },
                    function(){
                        
                    }
                )
            });

        })
    }
    
    // Stars Voting - ajax request
    this.updatePostRating_viaAjaxRequest = function( newRating, callback ){
        
        var postID = pxReviewsData.postID;

        $.ajax({
            type:"POST",
            url:pxreviews.ajax_url,
            data:{
                action:pxreviews.action,
                section:"vote",
                data:{
                    rating:parseInt(newRating),
                    postID:postID
                }
                
            },
            success:function( data ){
                callback( data );
            },
            dataType:"json"
        });

    }

}

function POSTS(){

	var $ = jQuery;

    this.blockPosts_hover = function(){
        
        $(".post-block, .post-list .post-featuredImage").each(function(){
            $(this).hover(function(){
                    $(this).find(".post-overlay").addClass("active");
                },
                function(){
                    $(this).find(".post-overlay").removeClass("active");
                }
            )
        });

    }

}

function isEmail( email ){

    return ( email.match(/(.+?)@(.+?)\.([a-zA-Z]+)$/) ? 1 : 0 );

}

function countDigits( number, jqContainer ){
	var $ = jQuery;
    $({countNum: 0}).animate({countNum: number}, {
    duration: 1400,
    easing:'linear',
    step: function() {
        // What todo on every count
        jqContainer.html(Math.round(this.countNum));
    },
    complete: function() {
        jqContainer.html(Math.round(number));
    }
    });

}

function isInt(value) {
  var x;
  return isNaN(value) ? !1 : (x = parseFloat(value), (0 | x) === x);
}