jQuery(function($) {
    var $checkoutForm = $('form.checkout'),
        $addPaymentMethodForm = $('form#add_payment_method'),
        ignoreNextEvent = false,
        $form;

    function getTimestamp() {
        return Math.round((new Date()).getTime() / 1000);
    }

    function isTokenizationEnabled()  {
        return $("#datacap-token").length > 0;
    }

    function onProcess() {
        var $isAddingNewCard = $("#wc-datacap-payment-token-new");

        if (ignoreNextEvent === true) {
            ignoreNextEvent = false;
            return true;
        }

        if (($addPaymentMethodForm.length > 0 || $isAddingNewCard.is(":checked") || $isAddingNewCard.length === 0) && isTokenizationEnabled()) {
            return tokenizeCard();
        }

        return true;
    }

    function tokenizeCard() {
        var $realCardNumber = $("#datacap-card-number"),
            $realCvv = $("#datacap-card-cvc"),
            $realCardExp = $("#datacap-card-expiry"),
            $publicKey = $("#datacap-public-key"),
            $tokenCardNumber = $("input[data-token='card_number']"),
            $tokenCvv = $("input[data-token='cvv']"),
            $tokenExpMonth = $("input[data-token='exp_month']"),
            $tokenExpYear = $("input[data-token='exp_year']");

        if (!$realCardNumber.is(".woocommerce-validated")) {
            $realCardNumber.trigger('change');
        }

        if (!$realCvv.is(".woocommerce-validated")) {
            $realCvv.trigger('change');
        }

        if (!$realCardExp.is(".woocommerce-validated")) {
            $realCardExp.trigger('change');
        }

        if ($realCardNumber.val().length === 0 || $realCvv.val().length === 0 || $realCardExp.val().length === 0) {
            var ts = getTimestamp();
            $form.unblock().prepend('<ul class="woocommerce-error" id="datacap-error-' + ts + '"><li>Please complete all required payment fields.</li></ul>');
            $("#datacap-error-" + ts).delay(4000).fadeOut("slow");
            return false;
        }

        if ($publicKey.length === 0 || $publicKey.val().length === 0) {
            alert('Error: Unable to continue checkout due to missing configuration. Please contact support.');
            return false;
        }

        var enteredExpiry = $realCardExp.val();

        $tokenCardNumber.val($realCardNumber.val().replace(' ', ''));
        $tokenExpMonth.val(enteredExpiry.substring(0, 2));
        $tokenExpYear.val('20' + enteredExpiry.substring(5));
        $tokenCvv.val($realCvv.val());

        DatacapWebToken.requestToken($publicKey.val(), "wc-datacap-cc-form", onTokenResponse);
        return false;
    }

    function onTokenResponse(response) {
        var $datacapToken = $('#datacap-token'),
            $datacapBrand = $('#datacap-brand'),
            $datacapExpMonth = $('#datacap-exp-month'),
            $datacapExpYear = $('#datacap-exp-year'),
            $datacapLast4 = $('#datacap-last-4'),
            error = '';

        if (response.Error) {
            error = "An error occurred while processing your card: " + response.Error;
        }

        if (!response.Token) {
            error = "An error occurred while processing your card. Please contact support if this persists.";
        }

        if (error.length > 0) {
            var ts = getTimestamp();
            $form.unblock().prepend('<ul class="woocommerce-error" id="datacap-error-' + ts + '"><li>' + error + '</li></ul>');
            $("#datacap-error-" + ts).delay(4000).fadeOut("slow");
            return;
        }

        $datacapToken.val(response.Token);
        $datacapBrand.val(response.Brand);
        $datacapExpMonth.val(response.ExpirationMonth);
        $datacapExpYear.val(response.ExpirationYear);
        $datacapLast4.val(response.Last4);

        ignoreNextEvent = true;
        $form.submit();
    }

    function onUpdatedCheckout() {
        $("#datacap-card-number, #datacap-card-expiry, #datacap-card-cvc").closest('.form-row').addClass('validate-required').removeClass('woocommerce-validated').trigger('change');
    }

    if ($checkoutForm.length > 0) {
        $form = $checkoutForm;
        $form.on('checkout_place_order_datacap', onProcess);
        $(document.body).on('updated_checkout', onUpdatedCheckout);
        onUpdatedCheckout();
    }

    if ($addPaymentMethodForm.length > 0) {
        $form = $addPaymentMethodForm;
        $form.on('submit', onProcess);
    }
});