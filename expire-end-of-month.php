<?php
/**
 * Forces expiration dates to always be set to the end of the month.
 *
 * NOTE: Changing the calculated expiration date does not change the renewal date
 * with the gateway, so using this alongside automatic renewals is not recommended.
 *
 * @param string     $expiration Calculated expiration date in MySQL format or 'none'.
 * @param int        $user_id    ID of the user the expiration date is being calculated for.
 * @param RCP_Member $member     Member object.
 *
 * @return string Modified expiration date.
 */
function ag_rcp_end_of_month_expiration( $expiration, $user_id, $member ) {

	// Don't make any changes if the expiration date is 'none'.
	if ( 'none' == $expiration ) {
		return $expiration;
	}

	// NOTE: Only use one of the following two examples.

	/**
	 * Example #1: End of month based on already calculated expiration
	 *
	 * This example forces the expiration date to use the end of *calculated* month.
	 * So if a user signs up for a one-month subscription on July 15th 2017, the regular
	 * expiration date would be August 15th 2017, and this snippet would change that to
	 * August 31st 2017.
	 */
	return date( 'Y-m-t 23:59:59', strtotime( $expiration ) );

	/**
	 * Example #2: End of current month
	 *
	 * This example forces the expiration date to use the end of the *current* month.
	 * So if a user signs up for a one-month subscription on July 15th 2017, the regular
	 * expiration date would be August 15th 2017, and this snippet would change that to
	 * July 31st 2017.
	 */
	return date( 'Y-m-t 23:59:59', current_time( 'timestamp' ) );

}

add_filter( 'rcp_member_calculated_expiration', 'ag_rcp_end_of_month_expiration', 10, 3 );