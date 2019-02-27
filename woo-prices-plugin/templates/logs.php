<?php
// Exit if accessed directly
if (!defined('ABSPATH')):
    exit;
endif; ?>
<section class="prices-buttons">
	<div class="prices-button">
		<form action="admin.php?page=prices_logs" method="post">
			<input name="download_log" class="button button-primary button-large" type="submit" value="Download logs file">
		</form>
		<?php echo ( isset( $_POST['download_log'] ) )? '<h2><a href="/wp-content/plugins/prices/logs/logs.txt">Open the log.txt</a></h2>': ''; ?>
	</div>
</section>
<?php if ( isset( $_POST['download_log'] ) ):
	Prices::download_logs();
endif;