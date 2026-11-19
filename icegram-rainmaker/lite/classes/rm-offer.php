<?php
global $icegram_rainmaker;

$ig_rm_plan = $icegram_rainmaker->get_plan();

// BFCM 2026 Campaign
if ( ( get_option( 'rm_offer_bfcm_2026_icegram' ) !== 'yes' ) && Rainmaker::is_offer_period( 'bfcm') && 'lite' === $ig_rm_plan ) { 
    $img_url = $this->plugin_url . '../assets/images/collect-bfcm-2026.jpg';
?>
    <style type="text/css">
        .ig_rm_offer { 
            width:70%;
            margin: 0 auto;
            text-align: center;
            padding-top: 0.8em;
        }
        .ig_rm_offer img {
            width: 100%;
        }
    </style>
    <div class="ig_rm_offer">
        <a target="_blank" href="?rm_dismiss_admin_notice=1&rm_option_name=rm_offer_bfcm_2026"><img style="margin:0 auto" src="<?php echo esc_url($img_url); ?>"/></a>
    </div>
<?php } ?>
