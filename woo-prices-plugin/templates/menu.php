<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

$prices_options = get_option( 'prices_options' );
if ( $prices_options['active'] === true ):

    $prices_client = Prices::client_setup( $prices_options ); ?>
<section class="prices-wrap">
	<form name="prices-action" action="admin.php?page=prices" method="post">
		<section class="prices-buttons">
			<div class="prices-button">
				<input name="download_products" id="prices-download-products" class="button button-primary button-large" type="submit" value="Download products">
			</div>
			<?php if ( isset( $_POST['download_products'] ) ) {?>
			<div class="prices-button">
				<input name="upload_products" id="prices-save-changes" class="button button-primary button-large" type="submit" value="Upload changes">
			</div>
			<div class="prices-button">
				<input name="create_product" id="prices-add-product" class="button button-primary button-large" type="submit" value="Add product">
			</div>
			<?php }?>
		</section>
<?php
	if ( isset( $_POST['download_products'] ) ) {
		Prices::show_products( $prices_client );
	} elseif ( isset( $_POST['upload_products'] ) ) {
		Prices::update_changes( $prices_client, $_POST );
	} else {
		return 0;
	} ?>
	</form>
</section>
<?php
elseif ( $prices_options['active'] === false ): ?>
<h2>Activate API! Go to - <a href="/wp-admin/admin.php?page=prices_settings">settings page</a>.</h2>
<?php endif;