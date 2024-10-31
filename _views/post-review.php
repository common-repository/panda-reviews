
<div class="px-post-review-wrapper" id="px-review-post-review-lightbox">
        
    <div class="px-review-box">
        
        <div class="px-review-headline">
            <div class="review-headline-title"><span>Thank you!</span> <label>Do you want to submit a review?</label></div>
            <hr class="silver"/>

            <span id="px-review-close-lightbox">Close X</span>

        </div>
        
        <form id="px-review-form">

            <input id="px-review-form-comment-type" type="hidden" name="type" value="review"/>
            <input id="px-review-form-comment-parent-id" type="hidden" name="parent_id" value="0"/>
            
            <textarea  id="px-review-comment" data-placeholder="Post a review" rows="6" name="review-message" >Post a review</textarea>

            <span id="px-review-error-msg"></span>
            <input type="text" data-validate="email" name="email-address" placeholder="Write your e-mail address" value=""/>
            <input type="text" data-validate="not-empty" name="username" value="" placeholder="Name"/>
            <button id="px-submit-review" type="button" class="px-reviews-post-review-button">Post a review</button>

        </form>

    </div>           

</div>
