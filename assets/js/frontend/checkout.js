jQuery(function($) {
    var $checkoutForm = $('form.checkout'),
        $addPaymentMethodForm = $('form#add_payment_method'),
        ignoreNextEvent = false,
        $form;

    function getTimestamp() {
        return Math.round((new Date()).getTime() / 1000);
    }

    function isTokenizationEnabled()  {
        return $("#monetary-token").length > 0;
    }

    function onProcess() {
        var $isAddingNewCard = $("#wc-monetary-payment-token-new");

        if (ignoreNextEvent === true) {
            ignoreNextEvent = false;
            return true;
        }

        if (($addPaymentMethodForm.length > 0 || $isAddingNewCard.is(":checked")) && isTokenizationEnabled()) {
            return tokenizeCard();
        }

        return true;
    }

    function tokenizeCard() {
        var $realCardNumber = $("#monetary-card-number"),
            $realCvv = $("#monetary-card-cvc"),
            $realCardExp = $("#monetary-card-expiry"),
            $publicKey = $("#monetary-public-key"),
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

        MonetaryWebToken.requestToken($publicKey.val(), "wc-monetary-cc-form", onTokenResponse);
        return false;
    }

    function onTokenResponse(response) {
        var $monetaryToken = $('#monetary-token'),
            $monetaryBrand = $('#monetary-brand'),
            $monetaryExpMonth = $('#monetary-exp-month'),
            $monetaryExpYear = $('#monetary-exp-year'),
            $monetaryLast4 = $('#monetary-last-4'),
            error = '';

        if (response.Error) {
            error = "An error occurred while processing your card: " + response.Error;
        }

        if (!response.Token) {
            error = "An error occurred while processing your card. Please contact support if this persists.";
        }

        if (error.length > 0) {
            var ts = getTimestamp();
            $form.unblock().prepend('<ul class="woocommerce-error" id="monetary-error-' + ts + '"><li>' + error + '</li></ul>');
            $("#monetary-error-" + ts).delay(4000).fadeOut("slow");
            return;
        }

        $monetaryToken.val(response.Token);
        $monetaryBrand.val(response.Brand);
        $monetaryExpMonth.val(response.ExpirationMonth);
        $monetaryExpYear.val(response.ExpirationYear);
        $monetaryLast4.val(response.Last4);

        ignoreNextEvent = true;
        $form.submit();
    }

    function onUpdatedCheckout() {
        $("#monetary-card-number, #monetary-card-expiry, #monetary-card-cvc").closest('.form-row').addClass('validate-required').removeClass('woocommerce-validated').trigger('change');
    }

    if ($checkoutForm.length > 0) {
        $form = $checkoutForm;
        $form.on('checkout_place_order_monetary', onProcess);
        $(document.body).on('updated_checkout', onUpdatedCheckout);
        onUpdatedCheckout();
    }

    if ($addPaymentMethodForm.length > 0) {
        $form = $addPaymentMethodForm;
        $form.on('submit', onProcess);
    }
});