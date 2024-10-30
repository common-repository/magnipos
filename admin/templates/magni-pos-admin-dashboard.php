<?php
// define("ACTIVE_API", "https://active.fluxbuilder.com/api/v1/active");
// define("DEACTIVE_API", "https://active.fluxbuilder.com/api/v1/deactive");
// define("ACTIVE_TOKEN", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmb28iOiJiYXIiLCJpYXQiOjE1ODY5NDQ3Mjd9.-umQIC6DuTS_0J0Jj8lcUuUYGjq9OXp3cIM-KquTWX0");
use MagniPos\Admin\Admin_Helper;
// Check if the form was submitted
if ( isset( $_POST['license_key'] ) ) {
	// Save the license key to the options table
	update_option( 'magni_pos_license_key', sanitize_text_field( $_POST['license_key'] ) );
	update_option( 'magni_pos_item_id', 32 );
}
$verified       = get_option( 'magni_pos_license_key' );
$activate_url   = Admin_Helper::get_activate_url();
$license_data   = empty( $verified ) ? null : Admin_Helper::get_license_data();
$pos_url        = Admin_Helper::getPOSUrl();
$site_url_valid = Admin_Helper::is_site_url_valid();
?>
<style>
/*----verify-----*/
.connection-verify-wrap{
	background-color: #ffffff45;
	padding: 20px 20px 20px;
	border: 1px solid #E4E4E5;
	border-radius: 5px;
	position: relative;
}
.magnipos_websfrm .connection-verify-wrap form {
	background-color: transparent;
	padding: 0;
	border: 0px solid #E4E4E5;
	border-radius: 0;
	position: relative;
}
.connection-verify-wrap h5 {
	font-size: 20px;
	margin: 0.67em 0 0;
	font-weight: 700;
	color: #212529;
	letter-spacing: -0.5px;
}
.connection-verify-wrap p{
	color:rgb(152 150 150 / 87%);
	font-size: 16px;
	margin-top: 0.3rem;
	margin-bottom: 1.5rem;
	display: block;
	font-weight: 400;
	line-height: 1.5;
}
.connection-verify-wrap form input[type=text]{
	font-family: "RobotoDraft", "Roboto", sans-serif;
	font-size: 16px;
	line-height: 35px;
	font-weight: 400;
	color: #495057;
	padding: 4px 16px;
	background-color: #fff;
	background-clip: padding-box;
	border: 1px solid #ced4da;
	border-radius: 4px;
	-webkit-appearance: none;
	-moz-appearance: none;
	appearance: none;
	box-shadow: none;
}
.connection-verify-wrap form input[type="submit"]{
	font-family: "RobotoDraft", "Roboto", sans-serif;
	font-size: 16px;
	font-weight:500;
	text-transform: capitalize;
	line-height: 28px;
	padding: 8px 16px;
	width: 100px;
	min-height: 44px;
	border: unset;
	border-radius: 4px;
	color: #fff;
	background: #2271b1;
}
.connection-verify-wrap form input[type="submit"]:hover {
	background: #135e96;
	border-color: #135e96;
}
p.or {
	color:rgb(152 150 150);
	font-size: 30px;
	margin: 0.6rem 0 1rem;
	display: block;
	font-weight: 500;
	line-height: 1.5;
	text-align: center;
}
/*-------connect_webs---------*/
.magnipos_websfrm {
	max-width: 700px;
	margin: 46px auto;
}

.magnipos_websfrm .magnipos_siteheader.text-center {
	text-align: center;
	margin-bottom: 40px;
}

.magnipos_websfrm .magnipos_siteheader.text-center img {
	max-width: 50px;
}

.magnipos_websfrm .magnipos_siteheader.text-center h1 {
	color: #1A1A1A;
	font-size: 22px;
	font-weight: 600;
}

.magnipos_websfrm .magnipos_siteheader form h1 {
	color: #1d2327;
	font-size: 18px;
	margin: 0.67em 0;
	font-weight: 600;
}

.magnipos_websfrm .form-group {
	margin-bottom: 20px;
}

.magnipos_websfrm form {
	background-color: #fff;
	padding: 20px 20px 20px;
	border: 1px solid #E4E4E5;
	border-radius: 5px;
	position: relative;
}

.magnipos_websfrm .magnipos_formgroupbg {
	background-color: #fff;
	padding: 15px 15px 0;
	border: 1px solid #E4E4E5;
	margin-top: 15px;
}

.magnipos_websfrm form h1 {
	color: #1d2327;
	font-size: 20px;
	margin: 0.67em 0;
	font-weight: 700;
}

.magnipos_websfrm form h1 svg {
	vertical-align: middle;
	margin-right: 10px;
}

.magnipos_websfrm .form-group textarea {
	background-color: #fff;
	border: 1px solid #E4E4E5;
	color: #000;
	border-radius: 2px;
	display: block;
	float: none;
	font-size: 14px;
	padding: 6px 10px;
	width: 100%;
	height: 80px;
	line-height: 1.3;
	font-weight: 500;
}

.magnipos_websfrm .form-group textarea::placeholder {
	color: #B7B7B7;
	font-weight: 500;
}

/*--update_btn--*/
.magnipos_websfrm .form-group button.magnipos_update {
	display: inline-block;
	height: 40px;
	line-height: 26px;
	padding-right: 25px;
	padding-left: 60px;
	position: relative;
	background-color: #FDD86D;
	color: rgb(255, 255, 255);
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border: 0;
	font-weight: 700;
	font-size: 14px;
}

.magnipos_websfrm .form-group button.magnipos_update span {
	position: absolute;
	left: 0;
	top: 0;
	width: 40px;
	height: 40px;
	line-height: 50px;
	background-color: #EBC862;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-bottom-left-radius: 5px;
	-moz-border-radius-top-left: 5px;
	-moz-border-radius-bottom-left: 5px;
	border-top-left-radius: 5px;
	border-bottom-left-radius: 5px;
	border-right: 1px solid #EBC862;
}

/*--connect_btn--*/
.magnipos_websfrm .magnipos_connect {
	display: inline-block;
	height: 40px;
	line-height: 26px;
	padding-left: 25px;
	padding-right: 60px;
	position: relative;
	background-color:#2bbf32;
	color: rgb(255, 255, 255);
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border: 0;
	font-weight: 700;
	font-size: 14px;
	margin-top: 20px;
	box-shadow: 0 3px 5px rgb(0 0 0 / 18%);
	transition: 0.5s all;
}

.magnipos_websfrm .magnipos_connect span {
	position: absolute;
	right: 0;
	top: 0;
	width: 40px;
	height: 40px;
	line-height: 50px;
	background-color: #19ab1f;
	-webkit-border-top-right-radius: 5px;
	-webkit-border-bottom-right-radius: 5px;
	-moz-border-radius-top-right: 5px;
	-moz-border-radius-bottom-right: 5px;
	border-top-right-radius: 5px;
	border-bottom-right-radius: 5px;
	border: 0;
	border-left: 1px solid #19ab1f;
	transition: 0.5s all;
}

.magnipos_websfrm .magnipos_connect:hover {
	background-color: #19ab1f;
	cursor: pointer;
	transition: 0.5s all;
}

.magnipos_websfrm .magnipos_connect:hover span {
	right: 5px;
	transition: 0.5s all;
}

/*--alert_message--*/
.magnipos_websfrm .magnipos_alertmsg {
	background-color: #FFF6F6;
	padding: 0 20px 10px;
	border: 1px solid #912D2B;
	border-radius: 5px;
	color: #912D2B;
	display: flex;
}

.magnipos_websfrm .magnipos_alertmsg p {
	font-size: 14px;
	font-weight: 500;
}

.magnipos_websfrm .magnipos_alertmsg b {
	font-size: 18px;
	font-weight: bold;
}

.magnipos_websfrm .magnipos_alertmsg a {
	color: #5C94D4;
	text-decoration: underline;
}

.magnipos_websfrm .magnipos_alertmsg svg {
	margin: 17px 20px 0px 0;
}

/*----------license_expaired-----------*/
/*--renew_btn--*/
.magnipos_licensexpiredbg .magnipos_renewbtn {
	display: inline-block;
	height: 40px;
	line-height: 26px;
	padding-left: 25px;
	padding-right: 60px;
	position: relative;
	background-color: #A5673F;
	color: rgb(255, 255, 255);
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border: 0;
	font-weight: 700;
	font-size: 14px;
	margin-top: 20px;
	box-shadow: 0 3px 5px rgb(0 0 0 / 18%);
	transition: 0.5s all;
}

.magnipos_licensexpiredbg .magnipos_renewbtn span {
	position: absolute;
	right: 0;
	top: 0;
	width: 40px;
	height: 40px;
	line-height: 50px;
	background-color: #905732;
	-webkit-border-top-right-radius: 5px;
	-webkit-border-bottom-right-radius: 5px;
	-moz-border-radius-top-right: 5px;
	-moz-border-radius-bottom-right: 5px;
	border-top-right-radius: 5px;
	border-bottom-right-radius: 5px;
	border: 0;
	border-left: 1px solid #905732;
	transition: 0.5s all;
}

.magnipos_licensexpiredbg .magnipos_renewbtn:hover {
	background-color: #905732;
	cursor: pointer;
	transition: 0.5s all;
}

.magnipos_licensexpiredbg .magnipos_renewbtn:hover span {
	right: 5px;
	transition: 0.5s all;
}

/*--alert_message2--*/
.magnipos_licensexpiredbg .magnipos_alertmsg {
	background-color: #FFF6F6;
	padding: 0 20px 10px;
	border: 1px solid #7960`37;
	border-radius: 5px;
	color: #796037;
	display: flex;
}

.magnipos_licensexpiredbg .magnipos_alertmsg p {
	font-size: 14px;
	font-weight: 500;
	color: #796037;
	padding: 0 30% 0 0;
}

.magnipos_licensexpiredbg .magnipos_alertmsg b {
	font-size: 18px;
	font-weight: bold;
}

.magnipos_licensexpiredbg .magnipos_alertmsg a {
	color: #5C94D4;
	text-decoration: none;
}

.magnipos_licensexpiredbg .magnipos_alertmsg svg {
	margin: 22px 20px 0px 0;
}

.magnipos_licensexpiredbg table {
	width: 100%;
	margin-top: 15px;
	border: 0px solid #E4E4E5 !important;
}

.magnipos_licensexpiredbg table,
.magnipos_licensexpiredbg th,
.magnipos_licensexpiredbg td {
	border: 1px solid #E4E4E5;
	text-align: left;
	color: #2D2D2D;
	font-size: 15px;
	margin: 0.67em 0;
	font-weight: bold;
}

.magnipos_licensexpiredbg td {
	font-weight: 500;
	font-size: 14px;
}

.magnipos_licensexpiredbg.shadowbg {
	box-shadow: rgb(100 100 111 / 20%) 0px 7px 40px 0px;
}

.magnipos_licensexpiredbg .magnipos_alertmsg svg {
	margin: 18px 20px 0px 0;
}

/*-------disconnect_btn---------*/
.magnipos_licensexpiredbg .magnipos_disconnectaccount {
	display: inline-block;
	width: 180px;
	height: 40px;
	line-height: 32px;
	background-color: transparent;
	border: 1px solid #A5673F;
	color: #A5673F;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	font-weight: 700;
	font-size: 13px;
	margin-top: 20px;
	text-align: left;
	margin-right: 6%;
	padding: 0 20px;
	position: absolute;
	right: 0;
	transition: 0.5s all;
}

.magnipos_licensexpiredbg .magnipos_disconnectaccount svg {
	position: absolute;
	right: 0;
	top: 5px;
	width: 30px;
	height: 30px;
	line-height: 30px;
	margin: 0;
}

.magnipos_licensexpiredbg .magnipos_disconnectaccount:hover {
	background-color: #fff5f5;
	cursor: pointer;
	box-shadow: 0 3px 5px rgb(0 0 0 / 18%);
	transition: 0.5s all;
}

/*--alert_message3--*/
.magnipos_licensevalid .magnipos_alertmsg {
	background-color: #FCFFF5;
	padding: 0 20px 10px;
	border: 1px solid #1A531B;
	border-radius: 5px;
	color: #1A531B;
	display: flex;
}

.magnipos_licensevalid .magnipos_alertmsg p {
	font-size: 14px;
	font-weight: 500;
	color: #1A531B;
}

.magnipos_licensevalid .magnipos_alertmsg b {
	font-size: 18px;
	font-weight: bold;
}

.magnipos_licensevalid .magnipos_alertmsg svg.tickk {
	margin: 12px 20px 0px 0;
}
</style>
<?php wp_nonce_field( 'magni_pos_register_product' ); ?>

	<?php
	if ( is_array( $license_data ) && $license_data['license'] != 'valid' ) {
		?>
<!----license_expired---->
<div class="magnipos_websfrm magnipos_licensexpiredbg shadowbg" style="margin-top:30px;">
	<form action="" method="post">
		<?php

		if ( isset( $_POST['but_deActive'] ) ) {
			delete_option( 'magni_pos_license_key' );
			delete_option( 'magni_pos_item_id' );
			header( 'Location: ' . $_SERVER['PHP_SELF'] . '?page=magnipos' );

		}
		?>
		<h1>
			<svg width="30" height="30" viewBox="0 0 24 24" fill="#000" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M8 18L10 16H12L13.3598 14.6394C14.03 14.873 14.7502 15 15.5 15C19.0899 15 22 12.0899 22 8.5C22 4.91015 19.0899 2 15.5 2C11.9101 2 9 4.91015 9 8.5C9 9.25243 9.12785 9.975 9.36301 10.6472L2 18V22H6L8 20V18Z"
					stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				<circle cx="17" cy="7" r="1" stroke="#fff" stroke-width="2" stroke-linecap="round"
					stroke-linejoin="round" />
			</svg>
			License Management
		</h1>
		<div class="magnipos_alertmsg">
			<svg width="50" height="50" viewBox="0 0 24 24" fill="none" class="tickk"
				xmlns="http://www.w3.org/2000/svg">
				<circle cx="12" cy="12" r="10" stroke="#796037" stroke-width="2" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M15 16L12.5858 13.5858C12.2107 13.2107 12 12.702 12 12.1716V6" stroke="#796037"
					stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
			<p>
				<b>Product license has invalid or expired</b><br>
				Please renew or buy new license key and continue all features.
			</p>

			<button class="btn magnipos_disconnectaccount" type="submit"
				onclick="return confirm('Are you sure to disconnect the license on this domain?');" name="but_deActive">
				Disconnect
				Account<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M18 18L24 24M24 24L30 30M24 24L30 18M24 24L18 30" stroke="#A5673F" stroke-width="2"
						stroke-linecap="round" />
				</svg>
			</button>
		</div>
		<?php
		if ( isset( $license_data['expires'] ) ) {
			?>
		<table cellpadding="14" cellspacing="0">
			<tr>
				<th>Expiration Date</th>
				<td><?php echo esc_html( $license_data['expires'] ); ?></td>
			</tr>
			<tr>
				<th>Licensed To</th>
				<td><?php echo esc_html( $license_data['customer_name'] ); ?></td>
			</tr>
			<tr>
				<th>License Key</th>
				<td><?php echo esc_html( get_option( 'magni_pos_license_key' ) ); ?></td>
			</tr>
		</table>
			<?php
		}
		?>
		<div style="text-align:right;">
			<button class="btn magnipos_renewbtn" onclick="location.href='<?php echo esc_url( $pos_url ); ?>'"
				type="button">
				Renew license
				<span><svg width="39" height="39" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M17 24H31M31 24L25.75 19M31 24L25.75 29" stroke="#fff" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</span>
			</button>
		</div>
	</form>
</div>

		<?php

	} elseif ( is_array( $license_data ) && $license_data['license'] == 'valid' ) {
		?>
<!----license_valid---->
<div class="magnipos_websfrm magnipos_licensexpiredbg shadowbg magnipos_licensevalid" style="margin-top:30px;">

	<form action="" method="post">
		<?php

		if ( isset( $_POST['but_deActive'] ) ) {
			delete_option( 'magni_pos_license_key' );
			delete_option( 'magni_pos_item_id' );
			header( 'Location: ' . $_SERVER['PHP_SELF'] . '?page=magnipos' );

		}
		?>
		<h1>
			<svg width="30" height="30" viewBox="0 0 24 24" fill="#000" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M8 18L10 16H12L13.3598 14.6394C14.03 14.873 14.7502 15 15.5 15C19.0899 15 22 12.0899 22 8.5C22 4.91015 19.0899 2 15.5 2C11.9101 2 9 4.91015 9 8.5C9 9.25243 9.12785 9.975 9.36301 10.6472L2 18V22H6L8 20V18Z"
					stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				<circle cx="17" cy="7" r="1" stroke="#fff" stroke-width="2" stroke-linecap="round"
					stroke-linejoin="round" />
			</svg>
			License Management
		</h1>
		<div class="magnipos_alertmsg">
			<svg fill="#21BA45" version="1.1" xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="60px" height="60px"
				viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
				<g id="_x37_7_Essential_Icons">
					<path id="Tick"
						d="M50,12c-21,0-38,17-38,38s17,38,38,38s38-17,38-38S71,12,50,12z M50,84c-18.8,0-34-15.2-34-34s15.2-34,34-34    s34,15.2,34,34S68.8,84,50,84z M72.9,37.1c-0.8-0.8-2-0.8-2.8,0L44.6,62.7L33.9,52c-0.8-0.8-2.1-0.8-2.8,0c-0.8,0.8-0.8,2.1,0,2.8    l12.1,12.1c0.4,0.4,0.9,0.6,1.4,0.6c0.5,0,1-0.2,1.4-0.6l26.9-27C73.7,39.1,73.7,37.9,72.9,37.1z" />
				</g>
				<g id="Guides">
				</g>
				<g id="Info">
					<g id="BORDER">
						<path fill="#21BA45"
							d="M1644-1210V474H-140v-1684H1644 M1652-1218H-148V482h1800V-1218L1652-1218z" />
					</g>
				</g>
			</svg>
			<p>
				<b>Product license is Valid</b><br>
				No action required.
			</p>
			<button class="btn magnipos_disconnectaccount" type="submit"
				onclick="return confirm('Are you sure to disconnect the license on this domain?');" name="but_deActive">
				Disconnect
				Account<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M18 18L24 24M24 24L30 30M24 24L30 18M24 24L18 30" stroke="#A5673F" stroke-width="2"
						stroke-linecap="round" />
				</svg>
			</button>
		</div>
		<table cellpadding="14" cellspacing="0">
			<tr>
				<th>Expiration Date</th>
				<td><?php echo esc_html( $license_data['expires'] ); ?></td>
			</tr>
			<tr>
				<th>Licensed To</th>
				<td><?php echo esc_html( $license_data['customer_name'] ); ?></td>
			</tr>
			<tr>
				<th>License Key</th>
				<td><?php echo esc_html( get_option( 'magni_pos_license_key' ) ); ?></td>
			</tr>
		</table>
	</form>
</div>
		<?php
	} else {
		?>
<!----connect_webs---->
<div class="magnipos_websfrm">
	<div class="magnipos_siteheader text-center">
	<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#2DCB70" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
			<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
		</svg>
		<h1 class="site-header__title">Thank you for installing MagniPOS API Plugin</h1>
	</div>
	<form>
		<h1>
			<svg width="30" height="30" viewBox="0 0 24 24" fill="#000" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M8 18L10 16H12L13.3598 14.6394C14.03 14.873 14.7502 15 15.5 15C19.0899 15 22 12.0899 22 8.5C22 4.91015 19.0899 2 15.5 2C11.9101 2 9 4.91015 9 8.5C9 9.25243 9.12785 9.975 9.36301 10.6472L2 18V22H6L8 20V18Z"
					stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				<circle cx="17" cy="7" r="1" stroke="#fff" stroke-width="2" stroke-linecap="round"
					stroke-linejoin="round" />
			</svg>
			License Management
		</h1>
		<div class="magnipos_alertmsg">
			<svg width="70" height="70" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="12" cy="12" r="10" stroke="#B2605E" stroke-width="2" />
				<path d="M5 19L19 5" stroke="#B2605E" stroke-width="2" />
			</svg>
			<p>
				<b>No active license</b><br>
				To activate this product, please visit the MagniPOS <a href="https://www.magnipos.com/">website</a> to learn more
			</p>
		</div>

		<div style="text-align: right;">
			<button class="btn magnipos_connect" onclick="location.href='<?php echo esc_url( $activate_url ); ?>'"
				type="button">
				Connect Now
				<span><svg width=" 39" height="39" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M17 24H31M31 24L25.75 19M31 24L25.75 29" stroke="#fff" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</span>
			</button>
		</div>
	</form>
	<p class="or">or</p>
	<div class="connection-verify-wrap">
		<h5 class="m-0">Enter License key</h5>
		<p class="mobile-text">You can get license key from your magnipos profile section.</p>
		<form method="post">		
			<input type="text" id="license_key" name="license_key" value="<?php echo esc_html( get_option( 'magni_pos_license_key' ) ); ?>" />
			<input type="submit" value="Verify" class="button button-primary" />
		</form>
	</div>
</div>
		<?php
	}
	?>
