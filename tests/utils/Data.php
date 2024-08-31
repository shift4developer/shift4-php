<?php
use Shift4\Request\ChargeRequest;
use Shift4\Request\CustomerRequest;
use Shift4\Request\CardRequest;
use Shift4\Request\FraudCheckDataRequest;
use Shift4\Request\PaymentMethodRequest;
use Shift4\Request\PaymentMethodRequestGooglePay;
use Shift4\Request\SubscriptionRequest;
use Shift4\Request\PlanRequest;
use Shift4\Request\ThreeDSecure;
use Shift4\Request\TokenRequest;
use Shift4\Request\BlacklistRuleRequest;
use Shift4\Request\CheckoutRequest;
use Shift4\Request\CheckoutRequestCharge;
use Shift4\Request\CheckoutRequestSubscription;
use Shift4\Request\CheckoutRequestCustomCharge;
use Shift4\Request\CheckoutRequestCustomAmount;
use Shift4\Request\ShippingRequest;
use Shift4\Request\BillingRequest;
use Shift4\Request\AddressRequest;
use Shift4\Request\ThreeDSecureRequest;
use Shift4\Request\CheckoutRequestThreeDSecure;
use Shift4\Request\CreditRequest;
use Shift4\Request\DisputeEvidenceRequest;
use Shift4\Response\PaymentMethod;

class Data
{
    const EMPTY_PNG_IMAGE = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQI12NgYGBgAAAABQABXvMqOgAAAABJRU5ErkJggg==';
    
    public static function chargeRequest()
    {
        $charge = new ChargeRequest();
        return $charge
            ->amount(499)
            ->currency('EUR')
            ->description('example-description')
            ->card(self::cardRequest())
            ->captured(true)
            ->threeDSecure((new ThreeDSecureRequest())
                ->requireAttempt(false)
                ->requireEnrolledCard(false)
                ->requireSuccessfulLiabilityShiftForEnrolledCard(false))
            ->metadata(self::metadata());
    }

    public static function customerRequest() {
        $customer = new CustomerRequest();
        return $customer
            ->email('email@example.com')
            ->description('example-description')
            ->metadata(self::metadata());
    }
    
    public static function customerWithCardRequest() {
        return self::customerRequest()
            ->card(self::cardRequest());
    }

    public static function cardRequest($number = '4242424242424242')
    {
        $card = new CardRequest();
        return $card
            ->number($number)
            ->expMonth(11)
            ->expYear(date('Y') + 1)
            ->cvc(123)
            ->cardholderName('cardholder-name')
            ->addressLine1('address-line-1')
            ->addressLine2('address-line-2')
            ->addressCity('address-city')
            ->addressState('address-state')
            ->addressZip('address-zip')
            ->addressCountry('CH')
            ->fraudCheckData(self::fraudCheckDataRequest());
    }

    public static function tokenRequest()
    {
        $token = new TokenRequest();
        return $token
        ->number('4242424242424242')
        ->expMonth(11)
        ->expYear(date('Y') + 1)
        ->cvc(123)
        ->cardholderName('cardholder-name')
        ->addressLine1('address-line-1')
        ->addressLine2('address-line-2')
        ->addressCity('address-city')
        ->addressState('address-state')
        ->addressZip('address-zip')
        ->addressCountry('CH')
        ->fraudCheckData(self::fraudCheckDataRequest());
    }

    public static function fraudCheckDataRequest()
    {
        $fraudCheckData = new FraudCheckDataRequest();
        return $fraudCheckData
        ->ipAddress('8.8.8.8')
        ->email('email@example.com')
        ->userAgent('user-agent')
        ->acceptLanguage('accept-language');
    }

    public static function subscriptionRequest($customer, $plan) 
    {
        $subscription = new SubscriptionRequest();
        return $subscription
            ->customerId($customer->getId())
            ->planId($plan->getId())
            ->quantity(1)
            ->captureCharges(false)
            ->trialEnd(time() + 10 * 24 * 60 * 60)
            ->metadata(self::metadata());
    }
    
    public static function planRequest()
    {
        $plan = new PlanRequest();
        return $plan
            ->amount(499)
            ->currency('EUR')
            ->interval('month')
            ->intervalCount(3)
            ->name('example-plan')
            ->trialPeriodDays(5)
            ->statementDescription('example-statement-description')
            ->metadata(self::metadata())
            ->billingCycles(7);
    }

    public static function blacklistRuleFingerprintRequest()
    {
        $rule = new BlacklistRuleRequest();
        return $rule
            ->ruleType('fingerprint')
            ->fingerprint('example-fingerprint-' . uniqid());
    }

    public static function metadata() {
        return array(
            'key' => 'value',
            'other-key' => 'other-value'
        );
    }
    
    public static function checkoutRequestWithCharge($customer) {
        $checkout = new CheckoutRequest();
        return $checkout
            ->charge((new CheckoutRequestCharge())
                ->amount(499)
                ->currency('EUR')
                ->capture(false)
                ->metadata(self::metadata()))
            ->customerId($customer->getId())
            ->rememberMe(true)
            ->threeDSecure((new CheckoutRequestThreeDSecure())
                ->enable(true)
                ->requireEnrolledCard(false)
                ->requireSuccessfulLiabilityShiftForEnrolledCard(false))
            ->termsAndConditionsUrl('http://example.com/');
    }
    
    public static function checkoutRequestWithSubscription($customer, $plan) {
        $checkout = new CheckoutRequest();
        return $checkout
            ->subscription((new CheckoutRequestSubscription())
                ->planId($plan->getId())
                ->captureCharges(false)
                ->metadata(self::metadata()))
            ->customerId($customer->getId())
            ->rememberMe(true)
            ->termsAndConditionsUrl('http://example.com/');
    }

    public static function checkoutRequestWithCustomCharge($customer) {
        $checkout = new CheckoutRequest();
        return $checkout
            ->customCharge((new CheckoutRequestCustomCharge())
                ->amountOptions(array(100, 200, 500, 1000))
                ->customAmount((new CheckoutRequestCustomAmount())
                    ->min(100)
                    ->max(10000))
                ->currency('EUR')
                ->capture(false)
                ->metadata(self::metadata()))
            ->customerId($customer->getId())
            ->rememberMe(true)
            ->termsAndConditionsUrl('http://example.com/');
    }

    public static function creditRequest()
    {
        $credit = new CreditRequest();
        return $credit
            ->amount(499)
            ->currency('EUR')
            ->description('example-description')
            ->card(self::cardRequest("4242000000011114"))
            ->metadata(self::metadata());
    }
    
    public static function disputeEvidenceRequest($fileUpload, $charge)
    {
        $disputeEvidenceRequest = new DisputeEvidenceRequest();
        return $disputeEvidenceRequest
            ->productDescription("example-product-description")
            ->customerEmail("example-customer-email")
            ->customerPurchaseIp("example-customer-purchase-ip")
            ->customerSignature($fileUpload->getId())
            ->billingAddress("example-billing-address")
            ->receipt($fileUpload->getId())
            ->customerCommunication($fileUpload->getId())
            ->serviceDate("example-service-date")
            ->serviceDocumentation($fileUpload->getId())
            ->duplicateChargeId($charge->getId())
            ->duplicateChargeDocumentation($fileUpload->getId())
            ->duplicateChargeExplanation("example-duplicate-charge-explanation")
            ->refundPolicy($fileUpload->getId())
            ->refundPolicyDisclosure("example-refund-policy-disclosure")
            ->refundRefusalExplanation("example-refund-refusal-explanation")
            ->cancellationPolicy($fileUpload->getId())
            ->cancellationPolicyDisclosure("example-cancellation-policy-disclosure")
            ->cancellationRefusalExplanation("example-cancellation-refusal-explanation")
            ->accessActivityLogs($fileUpload->getId())
            ->shippingAddress("example-shipping-address")
            ->shippingDate("example-shipping-date")
            ->shippingCarrier("example-shipping-carrier")
            ->shippingTrackingNumber("example-shipping-tracking-numberr")
            ->shippingDocumentation($fileUpload->getId())
            ->uncategorizedText("example-uncategorized-text")
            ->uncategorizedFile($fileUpload->getId());
    }
    
    public static function imageFile()
    {
        return __DIR__ . "/files/shift4.jpg";
    }
    
    public static function shippingRequest() {
        $shipping = new ShippingRequest();
        return $shipping
            ->name('shipping-name')
            ->address(self::addressRequest('shipping'));
    }

    public static function billingRequest() {
        $billing = new BillingRequest();
        return $billing
            ->name('billing-name')
            ->address(self::addressRequest('billing'))
            ->vat('billing-vat');
    }

    /**
     * @return PaymentMethodRequest
     */
    public static function googlePayPaymentMethodPanOnly()
    {
        $paymentMethod = new PaymentMethodRequest();
        return $paymentMethod
            ->type("google_pay")
            ->googlePay((new PaymentMethodRequestGooglePay())
                ->token("PAN_ONLY"));
    }

    /**
     * @return PaymentMethodRequest
     */
    public static function googlePayPaymentMethod3ds()
    {
        $paymentMethod = new PaymentMethodRequest();
        return $paymentMethod
            ->type("google_pay")
            ->googlePay((new PaymentMethodRequestGooglePay())
                ->token("CRYPTOGRAM_3DS"));
    }


    /**
     * @param $source PaymentMethod
     * @return PaymentMethodRequest
     */
    public static function threeDSecurePaymentMethod($source, $currency, $amount)
    {
        $paymentMethod = new PaymentMethodRequest();
        return $paymentMethod
            ->type("three_d_secure")
            ->source($source->getId())
            ->threeDSecure((new ThreeDSecure())
                ->currency($currency)
                ->amount($amount));
    }

    private static function addressRequest($prefix) {
        $address = new AddressRequest();
        return $address
            ->line1($prefix . '-address-line1')
            ->line2($prefix . '-address-line2')
            ->zip($prefix . '-address-zip')
            ->city($prefix . '-address-city')
            ->state($prefix . '-address-state')
            ->country('CH');
    }
}
