<?php
if ( ! isset( $_GET['px-reviews-tab'] ) ) {
	$active_tab = 'general-settings';
} else {
	$active_tab = sanitize_text_field( wp_unslash( $_GET['px-reviews-tab'] ) );
}

$admin_url = admin_url();
?>
<div class="px-reviews-wrapper">

	<img alt="reviews-logo" src="<?php echo esc_url( PXPR_PLUGIN_URL . 'assets/css/images/logo.png' ) ?>"/>

	<div class="px-nav-tabs">
		<a class="px-tab <?php echo esc_attr( px_set_as_active( $active_tab, 'manage-reviews', 'active' ) ); ?>" href="<?php echo esc_url( $admin_url . 'admin.php?page=px_posts_reviews&px-reviews-tab=manage-reviews' ) ?>">Reviews</a>
		<a class="px-tab <?php echo esc_attr( px_set_as_active( $active_tab, 'general-settings', 'active' ) );?>" href="<?php echo esc_url( $admin_url . 'admin.php?page=px_posts_reviews&px-reviews-tab=general-settings' ) ?>">General Settings</a>
		<a class="px-tab" target="_blank" href="http://wp.pixolette.com/plugins/panda-reviews">Help</a>
	</div>

<?php
switch ( $active_tab ) {

	case 'general-settings':

		include_once PXPR_PLUGIN_PATH . '_views/general-settings-wpbackend.php';

		break;

	case 'manage-reviews' :

		include PXPR_PLUGIN_PATH . '_views/manage-reviews-wpbackend.php';

		include PXPR_PLUGIN_PATH . '_views/pagination.php';

		break;

	case 'help':

		include PXPR_PLUGIN_PATH . '_views/help.php';

		break;

}
?>

</div><!-- END .px-reviews-wrapper -->
