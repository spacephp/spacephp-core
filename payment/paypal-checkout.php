<?php require_once(__DIR__ . '/helper.php');?>
<html>
    <head>
    </head>
    <body>
        <div id="paypal-button-container"></div>
        <script src="<?= site_url2()?>/assets/js/jquery.min.js"></script>
        <script>
            window.checkoutFormInfo = false;
            window.mecomProxySite = '<?=site_url2()?>';
            Object.defineProperty(document, "referrer", {
                get: function () {
                    return window.mecomProxySite;
                }
            });
        </script>
        <script src="https://www.paypal.com/sdk/js?client-id=<?=$client_id?>&currency=USD&intent=capture"></script>
        <script>
        function timeout(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
        paypal.Buttons({
            // Sets up the transaction when a payment button is clicked
            createOrder: async (data, actions) => {
                await timeout(500);
                if (! window.checkoutFormInfo) return actions.reject();
                let requireShipping = true;
                if (window.checkoutFormInfo.postcode != undefined) {
                    requireShipping = false;
                }
                var payerData = {
                    email_address: window.checkoutFormInfo.email
                }
                if (requireShipping) {
                    payerData.name = {surname: window.checkoutFormInfo.last_name,
                        given_name: window.checkoutFormInfo.first_name}
                }
                var purchaseUnits = [{
                    amount: {
                        value: (window.checkoutFormInfo.subtotal - window.checkoutFormInfo.discount + window.checkoutFormInfo.shipping_fee).toFixed(2),
                        currency_code: 'USD',
                        breakdown: {
                        item_total: {value: (window.checkoutFormInfo.subtotal - window.checkoutFormInfo.discount).toFixed(2), currency_code: 'USD'},
                        shipping: { value: window.checkoutFormInfo.shipping_fee, currency_code: "USD"}
                        }
                    },
                    items: window.checkoutFormInfo.items,
                }];
                if (requireShipping) {
                    purchaseUnits.shipping = {
                        name: {
                        "full_name": window.checkoutFormInfo.first_name + " " + window.checkoutFormInfo.last_name
                        },
                        address: {
                        "address_line_1": window.checkoutFormInfo.address_1,
                        "admin_area_2": window.checkoutFormInfo.city,
                        "postal_code": window.checkoutFormInfo.postcode,
                        "country_code": window.checkoutFormInfo.country
                        }
                    }
                }
                var applicationContext = {
                    brand_name: 'merchant',
                    user_action: 'CONTINUE',
                }
                
                if (!window.checkoutFormInfo.country || window.checkoutFormInfo.country.length === 0
                    || !window.checkoutFormInfo.city || window.checkoutFormInfo.city.length === 0
                ) {
                    applicationContext.shipping_preference = "NO_SHIPPING"
                } else {
                    payerData.address = {
                        country_code: window.checkoutFormInfo.country,
                        address_line_1: window.checkoutFormInfo.address_1,
                        address_line_2: "",
                        admin_area_1: window.checkoutFormInfo.state,
                        admin_area_2: window.checkoutFormInfo.city,
                        postal_code: window.checkoutFormInfo.postcode,
                    }
                    purchaseUnits[0].shipping = {
                        name: {
                            full_name: window.checkoutFormInfo.first_name + ' ' + window.checkoutFormInfo.last_name
                        },
                        address: {
                            country_code: window.checkoutFormInfo.country,
                            address_line_1: window.checkoutFormInfo.address_1,
                            address_line_2: "",
                            admin_area_1: window.checkoutFormInfo.state,
                            admin_area_2: window.checkoutFormInfo.city,
                            postal_code: window.checkoutFormInfo.postcode,
                        }
                    }
                }
                if (window.checkoutFormInfo.phone && window.checkoutFormInfo.phone.length) {
                    payerData.phone = {
                        phone_type: "HOME",
                        phone_number: {
                            national_number: window.checkoutFormInfo.phone.replace(/[^0-9]+/g, '')
                        },
                    }
                }
                window.orderData = {
                    purchase_units: purchaseUnits,
                    payer: payerData,
                    application_context: applicationContext
                }
                let orderD = {
                    purchase_units: [{
                        amount: {
                        value: (window.checkoutFormInfo.subtotal - window.checkoutFormInfo.discount + window.checkoutFormInfo.shipping_fee).toFixed(2),
                        currency_code: 'USD',
                        breakdown: {
                            item_total: {value: (window.checkoutFormInfo.subtotal - window.checkoutFormInfo.discount).toFixed(2), currency_code: 'USD'},
                            shipping: { value: window.checkoutFormInfo.shipping_fee, currency_code: "USD"}
                        }
                        },
                        shipping: {
                            name: {
                                "full_name": window.checkoutFormInfo.first_name + ' ' + window.checkoutFormInfo.last_name
                            },
                            address: {
                                "address_line_1": window.checkoutFormInfo.address_1,
                                "admin_area_2": window.checkoutFormInfo.city,
                                "postal_code": window.checkoutFormInfo.postcode,
                                "country_code": window.checkoutFormInfo.country
                            }
                        },
                        application_context: applicationContext
                    }]
                }
                console.log(window.orderData);
                return actions.order.create(window.orderData);
            },
            // Finalize the transaction after payer approval
            onApprove: (data, actions) => {
                return actions.order.capture().then(function(orderData) {
                    orderData.payment_method = 'paypal';
                    parent.postMessage({
                        name: 'paymentResult',
                        value: orderData
                    }, '*');
                });
            },
            onCancel: (data, actions) => {
                parent.postMessage('paypalCancel', '*');
            },
            onInit: function(data, actions) {},
            onClick: function (data, actions) {
                if (!window.checkoutFormInfo) {
                    parent.postMessage('paypalSubmit', '*');
                    //return actions.reject();
                }
            },
        }).render('#paypal-button-container');

        if (window.addEventListener) {
            window.addEventListener("message", listenerPaypal);
        } else {
            window.attachEvent("onmessage", listenerPaypal);
        }

        function listenerPaypal(event) {
            if ((typeof event.data === 'object') && event.data.name === 'submitForm') {
                window.checkoutFormInfo = event.data.value;
                console.log(window.checkoutFormInfo);
            }
        }
        </script>
    </body>
</html>