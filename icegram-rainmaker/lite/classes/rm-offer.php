<?php
global $icegram_rainmaker;
// BFCM 2022 Campaign
if ( ( get_option( 'rm_offer_bfcm_2023_icegram' ) !== 'yes' ) && Rainmaker::is_offer_period( 'bfcm') ) { 
    $img_url = $this->plugin_url . '../assets/images/bfcm-collect-free-pro-banner-2023.png';
    $ig_rm_plan = $icegram_rainmaker->get_plan();
    if( 'max' === $ig_rm_plan ){
        $img_url = $this->plugin_url .'../assets/images/bfcm-common-banner-2023.png';
    }
    //elseif( 'plus' === $ig_rm_plan || 'pro' === $ig_rm_plan ){
    //     $img_url = $this->plugin_url .'../assets/images/bfcm2021_pro.png';
    // }
?>
    <style type="text/css">
        .ig_rm_offer { 
            width:70%;
            margin: 0 auto;
            text-align: center;
            padding-top: 0.8em;
        }

    </style>
    <div class="ig_rm_offer">
        <a target="_blank" href="?rm_dismiss_admin_notice=1&rm_option_name=rm_offer_bfcm_2023"><img style="margin:0 auto" src="<?php echo $img_url; ?>"/></a>
    </div>
<?php } ?>

