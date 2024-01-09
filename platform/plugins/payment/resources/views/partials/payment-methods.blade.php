<ul class="list-group list_payment_method">
    {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
    'name' => $name,
    'amount' => $amount,
    'currency' => $currency,
    'selected' => PaymentMethods::getSelectedMethod(),
    'default' => PaymentMethods::getDefaultMethod(),
    'selecting' => PaymentMethods::getSelectingMethod(),
    ]) !!}

    {!! PaymentMethods::render() !!}

    <li class="list-group-item">
        <input class="magic-radio js_payment_method" id="credit_card" name="payment_method" type="radio" value="bank_transfer">
        <label class="text-start" for="credit_card">{{__('Pay with credit card')}}</label>
        <div class="credit_card_wrap payment_collapse_wrap" style="padding: 15px 0;">
            <div class="credit-card"></div>
        </div>
    </li>

    <li class="list-group-item">
        <input class="magic-radio js_payment_method" id="stc_pay" name="payment_method" type="radio" value="bank_transfer">
        <label class="text-start" for="stc_pay">{{__('Pay with STC Pay')}}</label>
        <div class="stc_pay_wrap payment_collapse_wrap" style="padding: 15px 0;">
            <div class="stc-pay"></div>
        </div>
    </li>
</ul>


<div class="hidden" id="payment-details" data-package="{{$package->name}}" data-price="{{$package->price}}" data-currency="{{$package->currency->title}}"></div>


<link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.7.3/moyasar.css" />
<script src="https://polyfill.io/v3/polyfill.min.js?features=fetch"></script>
<script src="https://cdn.moyasar.com/mpf/1.7.3/moyasar.js"></script>

<div class="hidden" id="payment-details" data-package="{{$package->name}}" data-price="{{$package->price}}" data-currency="{{$package->currency->title}}"></div>


<script>
    var paymentDetails = document.getElementById('payment-details');

    Moyasar.init({
        element: '.credit-card',
        // Amount in the smallest currency unit.
        // For example:
        // 10 SAR = 10 * 100 Halalas
        // 10 KWD = 10 * 1000 Fils
        // 10 JPY = 10 JPY (Japanese Yen does not have fractions)
        amount: parseFloat(paymentDetails.getAttribute('data-price')) * 100, // convert price to smallest currency unit
        currency: paymentDetails.getAttribute('data-currency'),
        description: paymentDetails.getAttribute('data-package'),
        publishable_api_key: 'pk_test_mD8Kz4T1ZuBk2jFE1bDMhgA3W9UEDmwfWxznWA3S',
        callback_url: '{{ route('payment.callback', ['packageId' => $package->id]) }}',
        methods: ['creditcard'],
        fixed_width: false, // optional, only for demo purposes
    })
</script>



<script>
    Moyasar.init({
        element: '.stc-pay',
        currency: paymentDetails.getAttribute('data-currency'),
        description: paymentDetails.getAttribute('data-package'),
        publishable_api_key: 'pk_test_mD8Kz4T1ZuBk2jFE1bDMhgA3W9UEDmwfWxznWA3S',
        callback_url: '{{ route('payment.callback', ['packageId' => $package->id]) }}',
        methods: ['stcpay'],
    });
</script>