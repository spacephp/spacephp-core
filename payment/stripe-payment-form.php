<?php
require_once(__DIR__ . '/helper.php');
?>
<html>
    <head>
        <link rel="stylesheet" href="https://kingtshirt.store/wp-content/plugins/mecom-payment-proxy/assets/css/stripe-payment-form.css?v=3.7.4"/>
    </head>
    <body>
    <form id="payment-form">
        <div id="payment-element">
        </div>
    </form>
    <script>
        window.stripePublicKey = "<?=$pk?>";
        window.mecomProxySite = "<?=site_url2()?>";
        Object.defineProperty(document, "referrer", {
            get: function () {
                return window.mecomProxySite;
            }
        });
    </script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe(window.stripePublicKey);
        var elements;
        initialize();

        document.querySelector("#payment-form").addEventListener("submit", handleSubmit);

        function initialize() {
            elements = stripe.elements();
            var cardElement = elements.create("card", {
                hidePostalCode: true,
                style: {
                    base: {
                        fontSize: "16px"
                    }
                }
            });
            cardElement.mount("#payment-element");
            window.cardElement = cardElement;
            cardElement.on('ready', function () {
                console.log('ready');
                parent.postMessage('loadedPaymentFormStripe', '*')
            });
            cardElement.on('change', function (event) {
                console.log('change');
                if (event.complete) {
                    parent.postMessage('paymentFormCompletedStripe', '*')
                } else {
                    parent.postMessage('paymentFormFailStripe', '*')
                }
            });
        }

        function handleSubmit(formData) {
            parent.postMessage('startSubmitPaymentStripe', '*')
            stripe.createPaymentMethod({
                type: 'card',
                card: window.cardElement,
                billing_details:  {
                    name: formData.first_name + ' ' + formData.last_name,
                    email: formData.email,
                    address: {
                        city: formData.city,
                        country: formData.country,
                        line1: formData.address_1,
                        postal_code: formData.postcode
                    },
                    phone: formData.phone,
                }
            }).then(function (e) {
                if (e.paymentMethod && e.paymentMethod.id) {
                    e.payment_method = 'stripe';
                    parent.postMessage({
                        name: 'paymentResult',
                        value: e
                    }, '*');
                } else if (e.error) {
                    if (['incomplete_number', 'invalid_number', 'incomplete_expiry', 'invalid_expiry', 'incomplete_cvc', 'invalid_cvc'].includes(e.error.code)) {
                        parent.postMessage('endSubmitPaymentStripe', '*')
                    } else {
                        parent.postMessage({
                            name: 'errorSubmitPaymentStripe',
                            value: e.error.message
                        }, '*');
                    }
                } else {
                    parent.postMessage('endSubmitPaymentStripe', '*')
                }
            })
        }

        // Listen event from client site
        if (window.addEventListener) {
            window.addEventListener("message", listener);
        } else {
            window.attachEvent("onmessage", listener);
        }

        function listener(event) {
            if ((typeof event.data === 'object') && event.data.name === 'submitForm') {
                handleSubmit(event.data.value);
            }
        }
    </script>
    </body>
</html>