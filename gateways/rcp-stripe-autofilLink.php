<?php
/* Disable the "Autofill Link" button Stripe has started adding to the credit card field */

add_filter('rcp_stripe_scripts', function($localize) {
    if (null == $localize['elementsConfig']) {
        $localize['elementsConfig'] = [];
    }
    $localize['elementsConfig']['disableLink'] = true;
    return $localize;
}, 10, 1);
