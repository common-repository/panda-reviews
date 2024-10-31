<?php
if ( $pages_count > 1 ) :
?>

	<div class="pxpagination-container">

		<ul class="pxpages">
			<?php
			$loop_max_val = min( $pages_count, 3 );
			for ( $i = 1; $i <= $loop_max_val; $i++ ) :?>
			
				<?php if ( $active_page === $i ) :?>
					<li class='active'><?php echo (int) $i;?></li>
				<?php else :?>
					<li><a href="<?php echo esc_url( $page_link . (int) $i ) ?>"><?php echo (int) $i; ?></a></li>
				<?php endif;?>
			
			<?php
			endfor;

			if ( $pages_count > 6 && $active_page >= 3 ) :

				$loop_max_val = min( ($active_page + 1), $pages_count );

				if ( $active_page >= 3 && $active_page <= 5  ) :

					for ( $i = 4; $i <= 6; $i++ ) :
						if ( $active_page === $i ) :?>
							<li class='active'><?php echo (int) $i; ?></li>
						<?php else :?>
							<li><a href="<?php echo esc_url( $page_link . (int) $i ) ?>"><?php echo (int) $i; ?></a></li>
						<?php endif;
					endfor;

				else :
					?>
					<li class="non-page">...</li>
					<?php
					for ( $i = $active_page - 1; $i <= $loop_max_val; $i++ ) :
						if ( $active_page === $i ) :?>
							<li class='active'><?php echo (int) $i; ?></li>
						<?php else :?>
							<li><a href="<?php echo esc_url( $page_link . (int) $i ) ?>"><?php echo (int) $i; ?></a></li>
						<?php endif;
					endfor;

				endif;
			endif;

			if ( ( $pages_count > 3 ) && ( ( $active_page + 1 ) <= $pages_count ) ) :
				?>
				<li class="non-page">...</li>
				<?php
				for ( $i = $pages_count - 2; $i <= $pages_count; $i++ ) :

					if ( ( $i <= ( $active_page + 1) && $active_page > 6 ) || $i <= 3 ) { continue; }

					?>	
					<li><a href="<?php echo esc_url( $page_link . (int) $i ) ?>"><?php echo (int) $i; ?></a></li>
					<?php

				endfor;
			elseif ( $active_page == $pages_count && $active_page < 6 ) :
				?>	
					<li class="active"><?php echo (int) $active_page; ?></li>
				<?php
			endif;

			?>
		</ul>

	</div>

<?php
endif;
