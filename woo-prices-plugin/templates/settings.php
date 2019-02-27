<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
    $prices_options = array(
        'active'     => ( isset( $_POST['prices_activate'] ) )? true: false,
        'domain'     => $_POST['prices_domain'],
        'user_key'   => $_POST['prices_userKey'],
        'secure_key' => $_POST['prices_secureKey'],
    );
    if ( update_option( 'prices_options', $prices_options ) ) {
        echo <<<doc
<div id="message" class="updated notice is-dismissible">
    <p>Settings <strong>updated</strong>.</p>
    <button type="button" class="notice-dismiss">
        <span class="screen-reader-text">Dismiss this notice.</span>
    </button>
</div>
doc;
        if( $prices_options['active'] ) {
            echo <<<doc
<div id="message" class="updated notice is-dismissible">
    <p>Remote API <strong>is active!</strong>.</p>
    <button type="button" class="notice-dismiss">
        <span class="screen-reader-text">Dismiss this notice.</span>
    </button>
</div>
doc;
        } else {
            echo <<<doc
<div id="message" class="updated notice is-dismissible">
    <p>Remote API <strong>deactivated!</strong>.</p>
    <button type="button" class="notice-dismiss">
        <span class="screen-reader-text">Dismiss this notice.</span>
    </button>
</div>
doc;
        }

    }
} else {
    $prices_options = get_option( 'prices_options' );
}
?>
<section class="prices-wrap">
    <form name="prices-settings" action="admin.php?page=prices_settings" method="post">
        <section class="prices-settings-field">
            <div class="prices-settings-caption">
                <h2><label for="prices-activate">Activate Remote API</label></h2>
            </div>
            <div class="prices-settings-input">
                <input id="prices-activate" name="prices_activate" type="checkbox" value="1" <?php echo ( $prices_options['active'] )? 'checked': '';?>>
            </div>
        </section>
        <section class="prices-settings-field">
            <div class="prices-settings-caption">
                <h2><label for="prices-domain">Remote Domain</label></h2>
            </div>
            <div class="prices-settings-input">
                <input id="prices-domain" name="prices_domain" type="text" value="<?php echo $prices_options['domain'];?>">
            </div>
        </section>
        <section class="prices-settings-field">
            <div class="prices-settings-caption">
                <h2><label for="prices-userKey">User Key</label></h2>
            </div>
            <div class="prices-settings-input">
                <input id="prices-userKey" name="prices_userKey" type="text" value="<?php echo $prices_options['user_key'];?>">
            </div>
        </section>
        <section class="prices-settings-field">
            <div class="prices-settings-caption">
                <h2><label for="prices-secureKey">Secure Key</label></h2>
            </div>
            <div class="prices-settings-input">
                <input id="prices-secureKey" name="prices_secureKey" type="text" value="<?php echo $prices_options['secure_key'];?>">
            </div>
        </section>
        <section class="prices-submit">
            <input type="submit" class="button button-primary button-large" value="Update">
        </section>
    </form>
</section>