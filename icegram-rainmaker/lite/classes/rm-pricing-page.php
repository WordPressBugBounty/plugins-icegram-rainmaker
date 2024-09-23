<?php
        $utm_medium = 'ig-rm-upgrade';
		$allowedtags = 'post';

		?>
		<style type="text/css">
			.update-nag {
				display: none;
			}
			.wrap.about-wrap.ig_rm {
				margin: 0 auto;
				max-width: 100%;
			}
			body{
				background-color: white;
			}
			.ig_rm_main_heading {
				font-size: 2em;
				background-color: #252f3f !important;
				color: #ffffff;
				text-align: center;
				font-weight: 500;
				margin: auto;
				padding-top: 0.75em;
				padding-bottom: 0.5em;
				/* max-width: 1375px; */
			}
			.ig_rm_discount_code {
				/* color: #6875F5; */
				font-weight: 600;
				font-size: 2.5rem;
			}
			.ig_rm_sub_headline {
				font-size: 1.6em;
				font-weight: 400;
				color: #00848D !important;
				text-align: center;
				line-height: 1.5em;
				margin: 0 auto 1em;
			}
			.ig_rm_row {
				/* padding: 1em !important;
				margin: 1.5em !important; */
				clear: both;
				position: relative;
			}
			#ig_rm_price_column_container {
				display: -webkit-box;
				display: -webkit-flex;
				display: -ms-flexbox;
				display: flex;
				max-width: 1190px;
				margin-right: auto;
				margin-left: auto;
				margin-top: 4em;
				padding-bottom: 1.2em;
			}
			.ig_rm_column {
				padding: 2em;
				margin: 0 1em;
				background-color: #fff;
				border: 1px solid rgba(0, 0, 0, 0.1);
				text-align: center;
				color: rgba(0, 0, 0, 0.75);
			}
			.column_one_fourth {
				width: 30%;
				border-radius: 3px;
				margin-right: 4%;
			}
			.ig_rm_last {
				margin-right: 0;
			}
			.ig_rm_price {
				margin: 1.5em 0;
				color: #1e73be;
			}
			.ig_rm_button {
				color: #FFFFFF !important;
				padding: 15px 32px;
				text-align: center;
				text-decoration: none;
				display: inline-block;
				font-size: 16px;
				font-weight: 500;
				margin: 2em 2px 1em 2px;
				cursor: pointer;
			}
			.ig_rm_button.green {
				background: #23B191;
				border-color: #23B191;
			}
			.ig_rm_button.green:hover {
				background: #66C78E;
				border-color: #66C78E;
			}

			.ig_rm_button.small {
				text-transform: uppercase !important;
				box-shadow: none;
				padding: 0.8em;
				font-size: 1rem;
				border-radius: 0.25rem;
				margin-top: 1em;
				font-weight: 600;
			}
			.ig_rm_discount_amount {
				font-size: 1.3em !important;
			}
			.dashicons.dashicons-yes {
				color: green;
				font-size: 2em;
			}
			.dashicons.dashicons-no-alt {
				color: #ed4337;
				font-size: 2em;
			}
			.dashicons.dashicons-yes.yellow {
				color: #BDB76B;
				line-height: unset;
			}
			.dashicons.dashicons-awards,
			.dashicons.dashicons-testimonial {
				margin-right: 0.25em !important;
				color: #15576F;
				font-size: 1.25em;
			}
			.ig_rm_license_name {
				font-size: 1.1em !important;
				color: #1a72bf !important;
				font-weight: 500 !important;
			}
			.ig_rm_old_price {
				font-size: 1.3em;
				color: #ed4337;
				vertical-align: top;
			}
			.ig_rm_new_price {
				font-size: 1.6em;
				padding-left: 0.2em;
				font-weight: 400;
			}
			.ig_rm_most_popular {
				position: absolute;
				right: 0px;
				top: -39px;
				background-color: #41495b;
				background-color: #596174;
				text-align: center;
				color: white;
				padding: 10px;
				font-size: 18px;
				border-top-right-radius: 4px;
				border-top-left-radius: 4px;
				font-weight: 500;
				width: 275px;
			}
			#ig-rm-testimonial {
				text-align: center;
			}
			.ig-rm-testimonial-content {
				width: 50%;
				margin: 0 auto;
				margin-bottom: 1em;
				background-color: #FCFEE9;
			}
			.ig-rm-testimonial-content img {
				width: 12% !important;
				border-radius: 9999px;
				margin: 0 auto;
			}
			.ig_rm_testimonial_headline {
				margin: 0.6em 0 !important;
				font-weight: 500 !important;
				font-size: 1.5em !important;
			}
			.ig_rm_testimonial_text {
				text-align: left;
				font-size: 1.2em;
				line-height: 1.6;
				padding: 1em;
			}
			.pricing {
				border-radius: 5px;
				position: relative;
				padding: 0.25em;
				margin: 2em auto;
				background-color: #fff;
				border: 1px solid rgba(0, 0, 0, 0.1);
				text-align: center;
				color: rgba(0, 0, 0, 0.75);
			}
			.pricing h4 {
				margin-top: 1em;
				margin-bottom: 1em;
			}
			.pricing del {
				font-size: 1.3em;
				color: grey;
			}
			.pricing h2 {
				margin-top: 0!important;
				margin-bottom: 0.5em;
				text-align: center;
				font-weight: 600;
				line-height: 1.218;
				color: #515151;
				font-size: 2.5em;
			}
			.pricing p {
				text-align: center;
				margin: 0em;
			}
			.pricing:hover{
				border-color: #15576F;
			}
			.pricing.scaleup{
				transform: scale(1.2);
			}
			.fidget.spin{
				animation: spin 1.2s 0s linear both infinite;
			}
			@keyframes spin {
				0% {
						transform: rotate(0deg);
					}
				100% {
						transform: rotate(360deg);
					}
			}
			table.ig_rm_feature_table {
				width: 90%;
				margin-left: 5%;
				margin-right: 5%;
			}
			table.ig_rm_feature_table th,
			table.ig_rm_feature_table tr,
			table.ig_rm_feature_table td,
			table.ig_rm_feature_table td span {
				padding: 0.5em;
				text-align: center !important;
				background-color: transparent !important;
				vertical-align: middle !important;
			}
			table.ig_rm_feature_table,
			table.ig_rm_feature_table th,
			table.ig_rm_feature_table tr,
			table.ig_rm_feature_table td {
				border: 1px solid #eaeaea;
			}
			table.ig_rm_feature_table.widefat th,
			table.ig_rm_feature_table.widefat td {
				color: #515151;
			}
			table.ig_rm_feature_table th {
				font-weight: bolder !important;
				font-size: 1.3em;
			}
			table.ig_rm_feature_table tr td {
				font-size: 15px;
			}
			table.ig_rm_feature_table th.ig_rm_features {
				background-color: #F4F4F4 !important;
				color: #A1A1A1 !important;
				width:16em;
			}
			table.ig_rm_feature_table th.ig_rm_free_features {
				background-color: #F7E9C8 !important;
				color: #D39E22 !important;
			}
			table.ig_rm_feature_table th.ig_rm_pro_features{
				background-color: #CCFCBF !important;
				color: #14C38E !important;
				width:16em;
			}
			table.ig_rm_feature_table th.ig_rm_starter_features {
				background-color: #DCDDFC !important;
				color: #6875F5 !important;
			}
			table.ig_rm_feature_table td{
				padding: 0.5em;
			}
			table.ig_rm_feature_table td.ig_rm_feature_name {
				text-transform: capitalize;
				padding:1em 2em;
			}
			table.ig_rm_feature_table td.ig_rm_free_feature_name {
				background-color: #FCF7EC !important;
				padding:1em 2em;
			}
			table.ig_rm_feature_table td.ig_rm_starter_feature_name {
				background-color: #F4F5FD !important;
				padding:1em 3em;
			}
			table.ig_rm_feature_table td.ig_rm_pro_feature_name {
				background-color: #E3FCBF !important;
				padding:1em 2em;
			}
			#ig_rm_product_page_link {
				text-align: center;
				font-size: 1.1em;
				margin-top: 2em;
				line-height: 2em;
			}
			.clr-a {
				color: #00848D !important;
			}
			.update-nag , .error, .updated{
				display:none;
			}
			table .dashicons {
				padding-top: 0 !important;
			}
			#wpcontent {
				padding-left: 0!important;
			}
			#ig-es-testimonial-others{
				margin-top: 4em;
			}
			#ig_rm_comparison_table{
				margin-top: 4em;
			}
			.ig_rm_renew_headline{
				font-size: 1em;
				font-weight: 400;
				color: #00848D !important;
				text-align: center;
				line-height: 1.5em;
				margin: 0 auto 1em;
				padding-bottom: 3em;
			}

		</style>

		<div class="wrap about-wrap ig_rm">
			<div class="ig_rm_row" id="ig-es-pricing">
				<div class="ig_rm_main_heading">
					<div style="display: inline-flex;">
						<div style="padding-right: 0.5rem;">🎉</div>
						<div style="line-height: 2.5rem;">
							<?php
								/* translators: %s: Offer text */
								echo sprintf( esc_html__( 'Congratulations! You just unlocked %s on Icegram Collect Premium!', 'icegram-rainmaker' ), '<span class="ig_rm_discount_code">' . esc_html__( '25% off', 'icegram-rainmaker' ) . '</span>' );
							?>
						</div>
						<div style="padding-left: 0.5rem;">🎉</div>
					</div>
					<div style="padding-top: 1em;font-size: 0.5em;"><?php echo esc_html__( '⏰ Limited time offer', 'icegram-rainmaker' ); ?></div>
				</div>
				<div id="ig_rm_price_column_container">
						<div class="ig_rm_column column_one_fourth pricing scaleup" style="border-color: #15576F;padding: 0;border-width: 0.2em;">
							<div style="text-align: center;background-color: #15576F;color: #FFF;padding: 1em;font-weight: 900;text-transform: uppercase;"> <?php echo esc_html__( 'Best Seller', 'icegram-rainmaker' ); ?> </div>
							<span class="ig_rm_plan"><h4 class="clr-a center"><?php echo esc_html__( 'Max', 'icegram-rainmaker' ); ?></h4></span>
							<span class="ig_rm_plan"><h4 class="clr-a center"><?php echo esc_html__( '3 sites (Annual)', 'icegram-rainmaker' ); ?></h4></span>
							<span class="ig_rm_price">
								<p><del class="center"><?php echo esc_html__( '$229', 'icegram-rainmaker' ); ?></del></p>
								<h2><?php echo esc_html__( '$172', 'icegram-rainmaker' ); ?></h2>
							</span>

							<div class="center">
								<a class="ig_rm_button small green center" href="https://www.icegram.com/?buy-now=33112&qty=1&page=6&coupon=rm-upgrade-25&with-cart=1&utm_source=ig_rm&utm_medium=<?php echo esc_attr( $utm_medium ); ?>&utm_campaign=rm-pro" target="_blank" rel="noopener"><?php esc_html_e( 'Get 25% Off', 'icegram-rainmaker' ); ?><span style="width: 1em; height: 1em; background-image: url('https://www.storeapps.org/wp-content/themes/storeapps/assets/images/fidget.svg'); display: inline-block; margin-left: 0.5em" class="fidget spin"></span></a>
							</div>
						</div>
                        <div class="ig_rm_column column_one_fourth pricing ig_rm_lifetime_price">
							<span class="ig_rm_plan"><h4 class="clr-a center"><?php echo esc_html__( 'Pro', 'icegram-rainmaker' ); ?></h4></span>
							<span class="ig_rm_plan"><h4 class="clr-a center"><?php echo esc_html__( '1 site (Annual)', 'icegram-rainmaker' ); ?></h4></span>
							<span class="ig_rm_price">
								<p><del class="center"><?php echo esc_html__( '$129', 'icegram-rainmaker' ); ?></del></p>
								<h2><?php echo esc_html__( '$97', 'icegram-rainmaker' ); ?></h2>
							</span>

							<div class="center">
								<a class="ig_rm_button small green center" href="https://www.icegram.com/?buy-now=19360&qty=1&page=6&coupon=rm-upgrade-25&with-cart=1&utm_source=ig_rm&utm_medium=<?php echo esc_attr( $utm_medium ); ?>&utm_campaign=rm-max" target="_blank" rel="noopener"><?php esc_html_e( 'Get 25% Off', 'icegram-rainmaker' ); ?></a>
							</div>
						</div>
				</div>
				<div class="ig_rm_renew_headline"><?php echo esc_html__( ' * Renew at same discounted price', 'icegram-rainmaker' ); ?></div>
			</div>
			<div class="ig_rm_row" id="ig-rm-testimonial">
				<div class="ig_rm_column ig-rm-testimonial-content">
					<?php
					echo wp_kses( apply_filters( 'ig_rm_pricing_page_testimonial_1', '
						<h3 class="ig_rm_testimonial_headline">' . __( 'Great plugins with absolutely amazing tech support.', 'icegram-rainmaker' ) . '</h3>
						<div class="ig_rm_testimonial_text">
							' . __( 'Great plugins with absolutely amazing tech support. (Icegram Engage & Collect in my case.) I found the support staff helpful, knowledgable, professional and friendly. Not only did they acknowledge and understand my concerns, but they went out of their way to ensure my needs were met. You don’t always find high quality customer service/support this good. A great experience all around.', 'icegram-rainmaker' ) . '<br><br>
							- ' . __( 'ttrusty', 'icegram-rainmaker' ) . '
						</div>' ), $allowedtags );
					?>
				</div>
			</div>
			<div class="ig_rm_row" id="ig_rm_comparison_table">
				<div class="ig_rm_sub_headline"><span class="dashicons dashicons-awards"></span><?php echo esc_html__( ' More powerful features with Icegram Collect Premium!', 'icegram-rainmaker' ); ?></div>
				<table class="ig_rm_feature_table wp-list-table widefat">
					<thead>
						<tr>
							<th class="ig_rm_features">
								<?php echo esc_html__( 'Features', 'icegram-rainmaker' ); ?>
							</th>
							<th class="ig_rm_free_features">
								<?php echo esc_html__( 'Free', 'icegram-rainmaker' ); ?>
							</th>
							<th class="ig_rm_starter_features">
								<?php echo esc_html__( 'Pro', 'icegram-rainmaker' ); ?>
							</th>
							<th class="ig_rm_pro_features">
								<?php echo esc_html__( 'Max', 'icegram-rainmaker' ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="ig_rm_feature_name">
								<strong><?php echo esc_html__( 'What\'s so special?', 'icegram-rainmaker' ); ?></strong>
							</td>
							<td class="ig_rm_free_feature_name">
								<?php echo esc_html__( 'Unlimited Contact Forms, Unlimited Subscription Forms and Unlimited Leads / Contacts.', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_starter_feature_name">
								<?php echo esc_html__( 'Everything in Free +', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_pro_feature_name">
								<?php echo esc_html__( 'Everything in Pro +', 'icegram-rainmaker' ); ?>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'Hubspot integration, Mailchimp integration, Campaign Monitor integration, Icegram Express (Email Subscribers) integration,Icegram Engage integration', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="ig_rm_starter_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="ig_rm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'Aweber integration, Custom Forms, Compact Layout', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="ig_rm_starter_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="ig_rm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'MadMimi integration', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<span class='dashicons dashicons-no-alt'></span>
							</td>
							<td class="ig_rm_starter_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="ig_rm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'Active Campaign integration', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<span class='dashicons dashicons-no-alt'></span>
							</td>
							<td class="ig_rm_starter_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="ig_rm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'Hubspot Pro integration', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<span class='dashicons dashicons-no-alt'></span>
							</td>
							<td class="ig_rm_starter_feature_name">
								<span class='dashicons dashicons-no-alt'></span>
							</td>
							<td class="ig_rm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'Captcha', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<span class='dashicons dashicons-no-alt'></span>
							</td>
							<td class="ig_rm_starter_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
							<td class="ig_rm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'Compact Layout', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<span class='dashicons dashicons-no-alt'></span>
							</td>
							<td class="ig_rm_starter_feature_name">
								<span class='dashicons dashicons-no-alt'></span>
							</td>
							<td class="ig_rm_pro_feature_name">
								<span class='dashicons dashicons-yes'></span>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'Support', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<?php echo esc_html__( 'WordPress Forum Support', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_starter_feature_name">
								<?php echo esc_html__( 'Premium Support (Email)', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_pro_feature_name">
								<?php echo esc_html__( 'VIP Support (Email + Facebook)', 'icegram-rainmaker' ); ?>
							</td>
						</tr>
						<tr>
							<td class="ig_rm_feature_name">
								<?php echo esc_html__( 'Pricing', 'icegram-rainmaker' ); ?>
							</td>
							<td class="ig_rm_free_feature_name">
								<span class=''>Free</span>
							</td>
							<td class="ig_rm_starter_feature_name">
								<div class="center">
									<a class="ig_rm_button small green center" href="https://www.icegram.com/?buy-now=19360&qty=1&page=6&coupon=rm-upgrade-25&with-cart=1&utm_source=ig_rm&utm_medium=<?php echo esc_attr( $utm_medium ); ?>&utm_campaign=rm-pro" target="_blank" style="text-transform: none;"><?php esc_html_e( 'Buy Pro', 'icegram-rainmaker' ); ?></a>
								</div>
							</td>
							<td class="ig_rm_pro_feature_name">
									<div class="center">
										<a class="ig_rm_button small green center" href="https://www.icegram.com/?buy-now=33112&qty=1&page=6&coupon=rm-upgrade-25&with-cart=1&utm_source=ig_rm&utm_medium=<?php echo esc_attr( $utm_medium ); ?>&utm_campaign=rm-max" target="_blank" style="text-transform: none;"><?php esc_html_e( 'Buy Max', 'icegram-rainmaker' ); ?></a>
									</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ig_rm_row" id="ig-es-testimonial-others">
				<div style="width: 70%; margin: 0 auto; display: flex; gap: 2em;">
					<div class="ig_rm_column ig-es-testimonial-content">
						<?php
							echo wp_kses( apply_filters( 'ig_rm_pricing_page_testimonial_2',
							'<div class="ig_rm_testimonial_text">
							    <div class="font-semibold text-lg pb-2">' . __('Easy to install and use') . '</div>
								' . __( 'Been using icegram for a while and found this add on. whilst it is early days the plugin is useful and helps build a subscriber list. Being able to integrate it into Icegram is a powerful feature and with a bit more development of the core and add ons it will be a very useful plugin. Worth a look.', 'icegram-rainmaker' ) . '<br><br>
								- ' . __( 'closeburn', 'icegram-rainmaker' ) . '
							</div>' ), $allowedtags );
						?>
					</div>
					<div class="ig_rm_column ig-es-testimonial-content">
						<?php
							echo wp_kses( apply_filters( 'ig_rm_pricing_page_testimonial_3',
							'<div class="ig_rm_testimonial_text">
							    <div class="font-semibold text-lg pb-2">' . __('Great for professional bloggers') . '</div>
								' . __( 'Icegram support has been just the best. I had trouble with some form, and when i enquired they not only helped my initial problem, they helped me out with custom code for my specific inquiry as well. That is something no one in my organization could even believe.
                                Thank you Moxa and team. You guys deserve all the help you can get.', 'icegram-rainmaker' ) . '<br><br>
								- ' . __( 'ashwintitan', 'icegram-rainmaker' ) . '
							</div>' ), $allowedtags );
						?>
					</div>
				</div>
			</div>
			<div class="ig_rm_row" id="ig_rm_product_page_link">
				<?php 
					/* translators: %s: Pricing page URL */
					echo sprintf( esc_html__( 'Not all form plugins store your leads submission data in-house which may result in losing the leads. Use %s to collect, save and organize your leads effortlessly. Own your data!', 'icegram-rainmaker' ), '<a style="color: #00848D;" target="_blank" href="https://www.icegram.com/collect/pricing/?utm_source=rm_in_app&utm_medium=in_app_pricing&utm_campaign=rm_pricing_footer">' . esc_html__( 'Icegram Collect', 'icegram-rainmaker' ) . '</a>' );
				?>
				<br>
			</div>
		</div>