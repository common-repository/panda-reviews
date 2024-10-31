
<?php
if ( false === $data ) :
	?>
	<h3>There are no reviews to show.</h3>
	<?php
else :
?>
	<div class="px_reviews_row">

		<h2>Manage Reviews</h2>

		<div class="reviews-headline" id="manage-reviews-headline" >
			<ul>
				<li>
					<input type="checkbox" id="check-all-reviews" class="px-checkbox" value=""/>
					<label for="check-all-reviews" class="px-checkbox-label"></label>Check All
				</li>
				<li>
					<select class="px-custom-select" data-class="px-custom-select-box">
						<option value="0">Action</option>
						<option value="read">Mark as seen</option>
						<option value="delete">Delete</option>
					</select>
				</li>
			</ul>
		</div>

		<div class="reviews-container">
			<?php
			foreach ( $reviews_data as $row ) :

				$class_type = ( 'review' === $row->comment_type ? 'review' : 'reply' );
				$date_time = new DateTime( esc_attr( $row->review_date ) );
				$featured_row = ( 'unread' === $row->status ? 'featured' : '' );
			?>
			
			
				<div class="review-row <?php echo esc_attr( $featured_row ); ?>">

					<div class="column user-info">
					
						<input type="checkbox" id="review-row-<?php echo (int) $row->reviewID; ?>" class="px-checkbox pxreview-checkbox" value="<?php echo (int) $row->reviewID; ?>"/>
						<label for="review-row-<?php echo (int) $row->reviewID; ?>" class="px-checkbox-label"></label>

						<strong><?php echo esc_attr( $row->username )?></strong>
						<span class="email-address"><?php echo esc_attr( $row->email )?></span>
						<span class="ip"><?php echo esc_attr( $row->user_ip )?></span>
					</div>

					<div class="column post-info">
						<a href="<?php echo esc_url( admin_url() . 'post.php?post=' . (int) $row->post_id . '&action=edit' )?>"><?php echo wp_trim_words( esc_attr( $row->post_title ), 7, '...' )?></a>
						<span class="<?php echo $class_type; ?>"><?php echo esc_attr( $row->comment_type ); ?></span>
					</div>

					<div class="column review-excerpt">
						<?php echo esc_attr( wp_trim_words( $row->comment, 48, '...' ) );?> 
					</div>

					<div class="column review-date">
						<?php echo $date_time->format( 'd M Y G:i' );?>
					</div>

					<div class="clear"></div>
			</div>
			<?php
			endforeach;
			?>	
		</div>

	</div>
<?php
endif;
