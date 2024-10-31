
<form id="pxgeneral-settings-form">
	<div class="px_reviews_row">

		<h2>General Settings:</h2>
		<div class="px-col px-left">
			<div class="general-settings-opt">
				<div class="px-opt">
					Display Voting System

					<div class="check-btn">

						<?php if ( $voting_system ) :?>						
						<input id="px-display-voting-system" checked="checked" type="checkbox" class="px-checkbox-slide green"  name="display-voting-system" value="1"/>
						<?php else :?>
						<input id="px-display-voting-system" type="checkbox" class="px-checkbox-slide green"  name="display-voting-system" value="1"/>
						<?php endif;?>

						<label for="px-display-voting-system">
							<span class="px-checkbox-slide-tracker"></span>
							<span class="px-checkbox-slide-bullet"></span>
						</label>
					</div>

				</div>
				<div class="px-opt">
					Display Review System

					<div class="check-btn">

						<?php if ( $reviews_system ) : ?>
						<input id="px-display-review-system" checked="checked" type="checkbox" class="px-checkbox-slide green"  name="display-review-system" value="1"/>
						<?php else : ?>
						<input id="px-display-review-system" type="checkbox" class="px-checkbox-slide green"  name="display-review-system" value="1"/>
						<?php endif;?>

						<label for="px-display-review-system">
							<span class="px-checkbox-slide-tracker"></span>
							<span class="px-checkbox-slide-bullet"></span>
						</label>

					</div>

				</div>
				<div class="px-opt">
					Show classic golden stars

					<div class="check-btn">

						<?php if ( $classic_stars ) : ?>
						<input id="px-show-golden-stars" checked="checked" type="checkbox" class="px-checkbox-slide green"  name="display-classic-stars" value="1"/>
						<?php else : ?>
						<input id="px-show-golden-stars" type="checkbox" class="px-checkbox-slide green"  name="display-classic-stars" value="1"/>
						<?php endif; ?>

						<label for="px-show-golden-stars">
							<span class="px-checkbox-slide-tracker"></span>
							<span class="px-checkbox-slide-bullet"></span>
						</label>

					</div>

				</div>
				<div class="px-opt">
					Select main color
					<input type="text" class="colorpick" value="<?php echo esc_attr( $main_color ); ?>" name="main-color"/>
				</div>
			</div>


		</div> <!-- END px-left -->
		
		<div class="px-col px-right">

			<ul class="px_reviews_posts-list">
				<?php
				foreach ( $post_types as $post_type ) :
					$checked = '';
					if ( isset( $settings ) && isset( $settings['active_posts'] ) && ! empty( $settings ) ) :
						$checked = ( in_array( $post_type->name, $settings['active_posts'], true ) ? 'checked="checked"' : '' );
					endif;
				?>
				<li>
					<input id="px_checkbox_<?php echo esc_attr( $post_type->name ) ?>" class="px-checkbox" <?php echo $checked ?> type="checkbox" name="px_reviews_post_types" value="<?php echo esc_attr( $post_type->name ) ?>"> <?php echo esc_attr( $post_type->labels->name )?>
					<label class="px-checkbox-label" for="px_checkbox_<?php echo esc_attr( $post_type->name ) ?>"><label>
				</li>
				
				<?php
				endforeach;
				?>
			</ul>    

			<button id="px_reviews_save_options_button" class="button-primary button">Save/Update</button>
			<span class="px-saved-msg">Saved!</span>
			<img id="px-btn-loading" style="display:none;" width="25" src="<?php echo esc_url( PXPR_PLUGIN_URL . 'assets/css/images/loading_light.gif' )?>"/>

		</div>

	</div><!-- End px_reviews_row -->

	<div class="px_reviews_row">

		<div class="rating-scheme">
			<h4>Select a scheme <i>(or select the classic golden stars that comes as defauls)</i></h4>

			<ul class="scheme-list stars">
				<?php $i = 0; ?>
				<?php foreach ( $like_schemes_template['stars'] as $row ) :?>
					<li>

						<?php if ( $row['value'] == $like_scheme ) : ?>
						<input class="px_radiobox-input" checked="checked" id="scheme-stars-<?php echo (int) $i; ?>" type="radio" name="scheme-type" value="<?php echo esc_attr( $row['value'] )?>">
						<?php else :?>
						<input class="px_radiobox-input" id="scheme-stars-<?php echo (int) $i; ?>" type="radio" name="scheme-type" value="<?php echo esc_attr( $row['value'] )?>">
						<?php endif;?>

						<label for="scheme-stars-<?php echo (int) $i; ?>" class="px_radiobox"></label>
						<img src="<?php echo esc_url( PXPR_PLUGIN_URL . 'assets/css/images/rating-schema/' . $row['icon'] )?>">
					</li>
					<?php $i++; ?>
				<?php endforeach; ?>
				
			</ul>

			<ul class="scheme-list hearts">
				<?php $i = 0; ?>
				<?php foreach ( $like_schemes_template['hearts'] as $row ) :?>
					<li>
	
						<?php if ( $row['value'] == $like_scheme ) : ?>
						<input class="px_radiobox-input" checked="checked" id="scheme-hearts-<?php echo (int) $i; ?>" type="radio" name="scheme-type" value="<?php echo esc_attr( $row['value'] )?>">
						<?php else :?>
						<input class="px_radiobox-input" id="scheme-hearts-<?php echo (int) $i; ?>" type="radio" name="scheme-type" value="<?php echo esc_attr( $row['value'] )?>">
						<?php endif;?>
	
						
						<label for="scheme-hearts-<?php echo (int) $i; ?>" class="px_radiobox"></label>
						<img src="<?php echo esc_url( PXPR_PLUGIN_URL . 'assets/css/images/rating-schema/' . $row['icon'] )?>">
					</li>
					<?php $i++; ?>
				<?php endforeach; ?>
				
			</ul>

			<ul class="scheme-list stars2">
				<?php $i = 0; ?>
				<?php foreach ( $like_schemes_template['stars-2'] as $row ) :?>
					<li>
						
						<?php if ( $row['value'] == $like_scheme ) : ?>
						<input class="px_radiobox-input" checked="checked" id="scheme-stars-2-<?php echo (int) $i; ?>" type="radio" name="scheme-type" value="<?php echo esc_attr( $row['value'] )?>">
						<?php else :?>
						<input class="px_radiobox-input" id="scheme-stars-2-<?php echo (int) $i; ?>" type="radio" name="scheme-type" value="<?php echo esc_attr( $row['value'] )?>">
						<?php endif;?>

						<label for="scheme-stars-2-<?php echo (int) $i; ?>" class="px_radiobox"></label>
						<img src="<?php echo esc_url( PXPR_PLUGIN_URL . 'assets/css/images/rating-schema/' . $row['icon'] )?>">
					</li>
					<?php $i++; ?>
				<?php endforeach; ?>
				
			</ul>

			<div class="clear"></div>

			<ul class="scheme-list likes">
				<?php $i = 0; ?>
				<?php foreach ( $like_schemes_template['likes'] as $row ) :?>
					<li>

						<?php if ( $row['value'] == $like_scheme ) : ?>
						<input class="px_radiobox-input" checked="checked" id="scheme-likes-<?php echo (int) $i; ?>" type="radio" name="scheme-type" value="<?php echo esc_attr( $row['value'] )?>">
						<?php else :?>
						<input class="px_radiobox-input" id="scheme-likes-<?php echo (int) $i; ?>" type="radio" name="scheme-type" value="<?php echo esc_attr( $row['value'] )?>">
						<?php endif;?>


						<label for="scheme-likes-<?php echo (int) $i; ?>" class="px_radiobox"></label>
						<img src="<?php echo esc_url( PXPR_PLUGIN_URL . 'assets/css/images/rating-schema/' . $row['icon'] )?>">
					</li>
					<?php $i++; ?>
				<?php endforeach; ?>
				
			</ul>			

			<label class="more-schemes-come-soon"> + more schemes to come soon!</label>
			<div class="clear"></div>

		</div> <!-- END .rating-scheme -->

	</div><!-- END .px_reviews_row -->
</form>
