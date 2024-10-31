
<ul class="rating px-rating <?php echo esc_attr( $scheme_class_name ); ?>">
	<li>Ranking</li>
	<li>
		<ul class="rating-list rated">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</li>

	<?php if ( 1 == $show_reviews ) : ?>
	<li class="reviewsCount">
		<label id="px-reviews-comments-count"><?php echo (int) $reviews_count; ?></label> <span>reviews</span>
	</li>
	<?php endif; ?>
</ul>
<hr class="silver">

<ul class="additional-info">
	<li>
		<strong id="px-reviews-votes-count">0</strong>
		<span>votes</span>
	</li>
	<li>
		<ul class="rating px-rating <?php echo esc_attr( $scheme_class_name ); ?>">
			<li>
				<ul class="rating-list active" data-post-id="">
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
				</ul>
			</li>
			<li><span id="px-rating-voted-message"><?php echo ( $user_has_voted ? 'Your vote' : 'Vote this' );?></span></li>    
		</ul>
	</li>
</ul>
<hr class="silver"/>
