<h1>JHL Auto Invite Slack Settings</h1>

<form class="jhl-sai" method="POST">
    <?php echo wp_nonce_field( 'jhl_sai_option_page_update' ); ?>

    <!-- <div style="display:inline-block;"> -->
    <div>
        <div style="width:150px;display:inline-block;float:left;">
            <label for="jhl_sai_add_user_fullname"><strong>Slack URL</strong></label>
        </div>
        <div style="display:inline-block;">
            <?php
            $jhl_sai_url = get_option( 'jhl_sai_url', '' );
            ?>
            <input type="text" name="jhl_sai_url" id="jhl_sai_url" value="<?php echo $jhl_sai_url; ?>">
            <br>
            <small>Enter the entire url, like <pre>https://foo.slack.com</pre></small>
        </div>
    </div>

    <div>
        <div style="width:150px;display:inline-block;float:left;">
            <label for="jhl_sai_meta_key"><strong>Slack API Token</strong></label>
        </div>
        <div style="display:inline-block;">
            <?php
            $jhl_sai_token = get_option( 'jhl_sai_token', '' );
            ?>
            <input type="text" name="jhl_sai_token" id="jhl_sai_token" value="<?php echo $jhl_sai_token; ?>">
            <br>
            <small>Get your classic token by going to this page</small>
        </div>
    </div>
    <div>
        <div style="width:150px;display:inline-block;float:left;">
            <label for="jhl_sai_channel"><strong>Channel</strong></label>
        </div>
        <div style="display:inline-block;">
            <?php
            $jhl_sai_channel = get_option( 'jhl_sai_channel', '' );
            ?>
            <input type="text" name="jhl_sai_channel" id="jhl_sai_channel" value="<?php echo $jhl_sai_channel; ?>">
            <br>
            <small>The channel you would like the user to join by default, get it by looking at the url.</small>
        </div>
    </div>

    <div style="clear:both;"></div>

    <input type="submit" value="Save" class="button button-primary button-large">

    <br><br>
    <hr>

    Once you have the settings above filled out, you can add a form that asks the user for their email by using the shortcode:

    <pre>[jhl_sai]</pre>

    <style>
    form.jhl-sai > div {
        padding-bottom : 15px;
    }
    form.jhl-sai label {
        display: inline-block;
        width: 150px;
    }
    </style>
</form>
