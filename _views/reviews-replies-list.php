<?php $inactive_class_name = ( ( isset( $reviews['count'] ) && (int) $reviews['count'] > 0 ) ? '' : 'inactive' ); ?>
<div class="px-reviews-replies-list <?php echo esc_attr( $inactive_class_name );?>">
	<?php
	if ( isset( $reviews['count'] ) && (int) $reviews['count'] > 0 ) :
	?>			
		<h2>Reviews (<span id="px-reviews-count"><?php echo esc_attr( $reviews['count'] ); ?></span>)</h2>
		<hr class="black">
	<?php
	endif;
	?>		
	<div class="px-reviews-container">
		<?php
		if ( isset( $reviews['data'] ) ) :

			$data_reviews = $reviews['data'];

			foreach ( $data_reviews as $review ) :

				$data_votes = null;
				$date_time = new DateTime( esc_attr( $review['review']->review_date ) );

				if ( isset( $review['review']->meta_value ) && '' !== $review['review']->meta_value ) {
					$data_votes = json_decode( $review['review']->meta_value, true );
				}

				$key = str_replace( '.', '', $review['review']->user_ip );

				$user_votes_data = array();

				if ( isset( $data_votes['votes'][ $key ] ) ) {
					$user_votes_data = $data_votes['votes'][ $key ];
				}

				?>
				
				<div class="px-user-review" data-id="<?php echo esc_attr( $review['review']->reviewID ); ?>">

					<div class="review-headline"> 
						<strong><?php echo esc_attr( $review['review']->username ); ?></strong>
						<ul class="rating px-rating <?php echo esc_attr( $scheme_class_name );?>">
							<li>
								<ul class="rating-list">

									<?php for ( $i = 0; $i < 5; $i++ ) : ?>
										<li <?php echo ( isset( $user_votes_data['vote'] ) && $i < (int) $user_votes_data['vote'] ? 'class="active"' : '' ); ?> ></li>
									<?php endfor;?>

								</ul>
							</li>
						</ul>
						<label class="date"><?php echo $date_time->format( 'd M Y G:i' );?></label>

					</div>
					
					<div class="review-body">
						<?php echo esc_html( $review['review']->comment ); ?>
					</div>
					
					<div class="px-replies">
						
						<button class="px-submit-reply" type="button">Reply</button>
							
						<div class="px-replies-wrapper <?php echo ( ( isset( $review['replies'] ) && count( $review['replies'] ) > 0 ) ? '' : 'no-replies' )?>" >
							<?php if ( isset( $review['replies'] ) && count( $review['replies'] ) > 0 ) : ?>
								<?php
								$count = 0;
								foreach ( $review['replies'] as $reply ) : ?>
									<?php $date_time = new DateTime( esc_attr( $reply->review_date ) ); ?>
									<div class="px-reply <?php  echo ( ( $count + 1 ) === count( $review['replies'] ) ? 'last-reply' : '' ); ?>">
										<label class="username"><span><?php echo esc_attr( $reply->username );?></span></label><label class="date"><?php echo $date_time->format( 'd M Y G:i' ); ?></label><?php echo esc_attr( $reply->comment ); ?>   
									</div>
									
								<?php
									$count++;
								endforeach; ?>
							
							<?php endif; ?>

						</div> <!-- END px-replies-wrapper -->

					</div><!-- END px-replies -->
				</div><!-- END px-user-review -->

		
			<?php
			endforeach;
		endif; ?>

	</div> <!-- End px-reviews-container -->

</div><!-- END px-reviews-replies-list -->
