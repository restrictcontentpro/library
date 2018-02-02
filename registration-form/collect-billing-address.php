<?php
/**
 * Plugin Name: Restrict Content Pro - Collect Billing Address
 * Description: Collect customers billing address during registration through Restrict Content Pro.
 * Version: 1.0
 * Author: Restrict Content Pro Team
 * License: GPL2
 */

class RCP_Billing_Address {

	/**
	 * RCP_Billing_Address constructor.
	 *
	 * @access public
	 * @since  1.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'rcp_after_password_registration_field', array( $this, 'fields' ) );
		add_action( 'rcp_form_errors', array( $this, 'error_checks' ) );
		add_filter( 'rcp_subscription_data', array( $this, 'subscription_data' ) );
		add_filter( 'rcp_paypal_args', array( $this, 'paypal_args' ), 10, 2 );
		add_action( 'rcp_edit_member_after', array( $this, 'member_details' ) );
		add_action( 'rcp_invoice_bill_to', array( $this, 'invoice' ), 10, 2 );
	}

	/**
	 * Add fields to registration form.
	 *
	 * @access public
	 * @since  1.0
	 * @return void
	 */
	public function fields() {
		$selected_country = isset( $_POST['rcp_country'] ) ? $_POST['rcp_country'] : '';
		?>
		<div id="rcp_user_address_fields">
			<p id="rcp_street">
				<label for="rcp_street"><?php _e( 'Address Line 1', 'rcp' ); ?></label>
				<input name="rcp_street" id="rcp_street" class="required" type="text" value="<?php echo ! empty( $_POST['rcp_street'] ) ? esc_attr( $_POST['rcp_street'] ) : ''; ?>"/>
			</p>
			<p id="rcp_street_2">
				<label for="rcp_street_2"><?php _e( 'Address Line 2', 'rcp' ); ?></label>
				<input name="rcp_street_2" id="rcp_street_2" type="text" value="<?php echo ! empty( $_POST['rcp_street_2'] ) ? esc_attr( $_POST['rcp_street_2'] ) : ''; ?>"/>
			</p>
			<p id="rcp_city">
				<label for="rcp_city"><?php _e( 'City', 'rcp' ); ?></label>
				<input name="rcp_city" id="rcp_city" class="required" type="text" value="<?php echo ! empty( $_POST['rcp_city'] ) ? esc_attr( $_POST['rcp_city'] ) : ''; ?>"/>
			</p>
			<p id="rcp_country">
				<label for="rcp_country"><?php _e( 'Country', 'rcp' ); ?></label>
				<select name="rcp_country" id="rcp_country">
					<?php foreach ( $this->get_countries() as $key => $country ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php checked( $selected_country, $key ); ?>><?php echo $country; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p id="rcp_state">
				<label for="rcp_state"><?php _e( 'State / Province', 'rcp' ); ?></label>
				<input name="rcp_state" id="rcp_state" class="required" type="text" value="<?php echo ! empty( $_POST['rcp_state'] ) ? esc_attr( $_POST['rcp_state'] ) : ''; ?>"/>
			</p>
			<p id="rcp_zip">
				<label for="rcp_zip"><?php _e( 'Zip / Postal Code', 'rcp' ); ?></label>
				<input name="rcp_zip" id="rcp_zip" class="required" type="text" value="<?php echo ! empty( $_POST['rcp_zip'] ) ? esc_attr( $_POST['rcp_zip'] ) : ''; ?>"/>
			</p>
		</div>
		<?php
	}

	/**
	 * Check for errors when submitting the registration form - all fields are required.
	 *
	 * @param array $data Submitted data.
	 *
	 * @access public
	 * @since  1.0
	 * @return void
	 */
	public function error_checks( $data ) {
		if ( is_user_logged_in() ) {
			return;
		}

		if ( empty( $data['rcp_street'] ) ) {
			rcp_errors()->add( 'empty_address', __( 'Please enter your address', 'rcp' ), 'register' );
		}
		if ( empty( $data['rcp_city'] ) ) {
			rcp_errors()->add( 'empty_city', __( 'Please enter your city', 'rcp' ), 'register' );
		}
		if ( empty( $data['rcp_state'] ) ) {
			rcp_errors()->add( 'empty_state', __( 'Please enter your state', 'rcp' ), 'register' );
		}
		if ( empty( $data['rcp_country'] ) || $data['rcp_country'] == '*' ) {
			rcp_errors()->add( 'empty_country', __( 'Please select your country', 'rcp' ), 'register' );
		}
		if ( empty( $data['rcp_zip'] ) ) {
			rcp_errors()->add( 'empty_zip', __( 'Please enter your zip code', 'rcp' ), 'register' );
		}
	}

	/**
	 * Add address fields to gateway.
	 *
	 * @param array $subscription_data Data sent to gateway.
	 *
	 * @access public
	 * @since  1.0
	 * @return array
	 */
	public function subscription_data( $subscription_data ) {

		$subscription_data['address']            = array();
		$subscription_data['address']['line1']   = isset( $_POST['rcp_street'] ) ? sanitize_text_field( $_POST['rcp_street'] ) : '';
		$subscription_data['address']['line2']   = isset( $_POST['rcp_street_2'] ) ? sanitize_text_field( $_POST['rcp_street_2'] ) : '';
		$subscription_data['address']['line2']   = isset( $_POST['rcp_city'] ) ? sanitize_text_field( $_POST['rcp_city'] ) : '';
		$subscription_data['address']['state']   = isset( $_POST['rcp_state'] ) ? sanitize_text_field( $_POST['rcp_state'] ) : '';
		$subscription_data['address']['zip']     = isset( $_POST['rcp_zip'] ) ? sanitize_text_field( $_POST['rcp_zip'] ) : '';
		$subscription_data['address']['country'] = isset( $_POST['rcp_country'] ) ? sanitize_text_field( $_POST['rcp_country'] ) : '';

		update_user_meta( $subscription_data['user_id'], 'rcp_address', $subscription_data['address'] );

		return $subscription_data;
	}

	/**
	 * Send address details to PayPal.
	 *
	 * @param array                      $args    Data sent to PayPal.
	 * @param RCP_Payment_Gateway_PayPal $gateway PayPal gateway class.
	 *
	 * @access public
	 * @since  1.0
	 * @return array
	 */
	public function paypal_args( $args, $gateway ) {

		$subscription_data = $gateway->subscription_data;

		unset( $args['no_shipping'] );
		unset( $args['tax'] );
		$args['address1'] = $subscription_data['address']['line1'];
		$args['address2'] = $subscription_data['address']['line2'];
		$args['city']     = $subscription_data['address']['city'];
		$args['state']    = $subscription_data['address']['state'];
		$args['zip']      = $subscription_data['address']['zip'];
		$args['country']  = $subscription_data['address']['country'];

		return $args;
	}

	/**
	 * Display address details on admin Edit Member screen.
	 *
	 * @param int $user_id ID of the user being displayed.
	 *
	 * @access public
	 * @since  1.0
	 * @return void
	 */
	public function member_details( $user_id ) {

		$address = get_user_meta( $user_id, 'rcp_address', true );

		if ( empty( $address ) ) {
			return;
		}
		?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<?php _e( 'Address', 'rcp' ); ?>
			</th>
			<td>
				<?php foreach ( $address as $line ) : ?>
					<?php echo esc_html( $line ) . '<br/>'; ?>
				<?php endforeach; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Returns an array of available countries.
	 *
	 * @access public
	 * @since  1.0
	 * @return array
	 */
	public function get_countries() {
		$countries = array(
			'*'  => __( 'Choose', 'rcp' ),
			'US' => 'United States',
			'CA' => 'Canada',
			'GB' => 'United Kingdom',
			'AF' => 'Afghanistan',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei Darrussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CD' => 'Congo, Democratic People\'s Republic',
			'CG' => 'Congo, Republic of',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Cote d\'Ivoire',
			'HR' => 'Croatia/Hrvatska',
			'CU' => 'Cuba',
			'CY' => 'Cyprus Island',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'TP' => 'East Timor',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'GQ' => 'Equatorial Guinea',
			'SV' => 'El Salvador',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GR' => 'Greece',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard and McDonald Islands',
			'VA' => 'Holy See (City Vatican State)',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Lao People\'s Democratic Republic',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libyan Arab Jamahiriya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourgh',
			'MO' => 'Macau',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'Mv' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia',
			'MD' => 'Moldova, Republic of',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'AN' => 'Netherlands Antilles',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'KR' => 'North Korea',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territories',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Phillipines',
			'PN' => 'Pitcairn Island',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Reunion Island',
			'RO' => 'Romania',
			'RU' => 'Russian Federation',
			'RW' => 'Rwanda',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovak Republic',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia',
			'KP' => 'South Korea',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen Islands',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TH' => 'Thailand',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'UY' => 'Uruguay',
			'UM' => 'US Minor Outlying Islands',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'VG' => 'Virgin Islands (British)',
			'VI' => 'Virgin Islands (USA)',
			'WF' => 'Wallis and Futuna Islands',
			'EH' => 'Western Sahara',
			'WS' => 'Western Samoa',
			'YE' => 'Yemen',
			'YU' => 'Yugoslavia',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe'
		);

		return $countries;
	}

	/**
	 * Display billing address on the invoice under "Bill To".
	 *
	 * @param object     $rcp_payment Payment from the database.
	 * @param RCP_Member $member      Member object.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function invoice( $rcp_payment, $member ) {

		$address = get_user_meta( $member->ID, 'rcp_address', true );

		if ( empty( $address ) ) {
			return;
		}
		?>
		<p>
			<?php foreach ( $address as $line ) : ?>
				<?php echo esc_html( $line ) . '<br/>'; ?>
			<?php endforeach; ?>
		</p>
		<?php

	}

}

new RCP_Billing_Address;