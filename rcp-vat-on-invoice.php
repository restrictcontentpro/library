<?php
/**
 * Plugin Name: Restrict Content Pro - VAT on Invoices
 * Description: Displays on the invoice how much of the payment was for VAT.
 * Version: 1.0
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * License: GPL2
 */

/**
 * Displays on the invoice how much of the payment was for VAT. This assumes you've bundled
 * the VAT into the cost of the subscription level. This will calculate your chosen
 * VAT percentage (20% by default) from the full cost of the subscription level and
 * display the amount of money on the invoice right above the total amount.
 *
 * @param object $rcp_payment Payment object from the database.
 *
 * @return void
 */
function ag_rcp_display_vat_on_invoice( $rcp_payment ) {
	// Don't show VAT if payment is free.
	if ( empty( $rcp_payment->amount ) ) {
		return;
	}

	$vat_percentage = 20; // This is 20% VAT by default. Adjust number here to change VAT percentage.

	// Calculate VAT amount.
	$vat = $rcp_payment->amount / ( 1 + ( $vat_percentage / 100 ) );
	$vat = round( ( $vat - $rcp_payment->amount ) * -1, 2 );
	?>
	<tr>
		<td class="name"><?php printf( __( '%d%% VAT' ), $vat_percentage ); ?></td>
		<td class="price">
			<?php
			echo rcp_currency_filter( $vat );
			?>
		</td>
	</tr>
	<?php
}

add_action( 'rcp_invoice_items_before_total_price', 'ag_rcp_display_vat_on_invoice' );