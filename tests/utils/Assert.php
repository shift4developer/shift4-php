<?php

class Assert extends \PHPUnit\Framework\Assert
{

    /**
     * @param \Shift4\Request\ChargeRequest $request            
     * @param \Shift4\Response\Charge $charge            
     */
    public static function assertCharge($request, $charge)
    {
        self::assertNotNull($charge->getId());
        self::assertNotNull($charge->getCreated());
        
        self::assertEquals($request->getAmount(), $charge->getAmount());
        self::assertEquals($request->getCurrency(), $charge->getCurrency());
        self::assertEquals($request->getDescription(), $charge->getDescription());
        self::assertEquals($request->getCustomerId(), $charge->getCustomerId());
        self::assertEquals($request->getCaptured(), $charge->getCaptured());
        self::assertEquals($request->getMetadata(), $charge->getMetadata());
        
        self::assertCard($request->getCard(), $charge->getCard());
        
        self::assertShipping($request->getShipping(), $charge->getShipping());
        self::assertBilling($request->getBilling(), $charge->getBilling());
    }

    /**
     * @param \Shift4\Request\RefundRequest $request
     * @param \Shift4\Response\Charge $charge
     * @param \Shift4\Response\Refund $refund
     */
    public static function assertRefund($request, $charge, $refund, $expectChargeId = true)
    {
        self::assertNotNull($refund->getId());
        self::assertNotNull($refund->getCreated());
        
        self::assertEquals($request->getAmount(), $refund->getAmount());
        self::assertEquals($charge->getCurrency(), $refund->getCurrency());
        if ($expectChargeId) {
            self::assertEquals($charge->getId(), $refund->getCharge());
            self::assertEquals($request->getChargeId(), $refund->getCharge());
        }
        self::assertEquals($request->getReason(), $refund->getReason());
        self::assertEquals('successful', $refund->getStatus());
    }
    
    /**
     * @param \Shift4\Request\CustomerRequest $request            
     * @param \Shift4\Response\Customer $customer            
     */
    public static function assertCustomer($request, $customer)
    {
        self::assertNotNull($customer->getId());
        self::assertNotNull($customer->getCreated());
        self::assertFalse($customer->getDeleted());
        
        self::assertEquals($request->getEmail(), $customer->getEmail());
        self::assertEquals($request->getDescription(), $customer->getDescription());
        self::assertEquals($request->getMetadata(), $customer->getMetadata());
        
        if ($request->getCard()) {
            self::assertCard($request->getCard(), $customer->getDefaultCard());
        }
    }    
        
    /**
     * @param \Shift4\Request\CardRequest $request            
     * @param \Shift4\Response\Card $card            
     */
    public static function assertCard($request, $card)
    {
        self::assertNotNull($card->getId());
        self::assertNotNull($card->getCreated());
        self::assertFalse($card->getDeleted());
        
        self::assertEquals(substr($request->getNumber(), 0, 6), $card->getFirst6());
        self::assertEquals(substr($request->getNumber(), -4, 4), $card->getLast4());
        self::assertMatchesRegularExpression('/\w+/', $card->getFingerprint());
        self::assertMatchesRegularExpression('/\w+/', $card->getType());
        self::assertMatchesRegularExpression('/\w+/', $card->getBrand());
        self::assertEquals($request->getExpMonth(), $card->getExpMonth());
        self::assertEquals($request->getExpYear(), $card->getExpYear());
        self::assertEquals($request->getCardholderName(), $card->getCardholderName());
        self::assertEquals($request->getAddressLine1(), $card->getAddressLine1());
        self::assertEquals($request->getAddressLine2(), $card->getAddressLine2());
        self::assertEquals($request->getAddressCity(), $card->getAddressCity());
        self::assertEquals($request->getAddressZip(), $card->getAddressZip());
        self::assertEquals($request->getAddressState(), $card->getAddressState());
        self::assertEquals($request->getAddressCountry(), $card->getAddressCountry());
        
        self::assertFraudCheckData($request->getFraudCheckData(), $card->getFraudCheckData());
    }

    /**
     * @param \Shift4\Request\TokenRequest $request
     * @param \Shift4\Response\Token $token
     */
    public static function assertToken($request, $token)
    {
        self::assertNotNull($token->getId());
        self::assertNotNull($token->getCreated());
    
        self::assertEquals(substr($request->getNumber(), 0, 6), $token->getFirst6());
        self::assertEquals(substr($request->getNumber(), -4, 4), $token->getLast4());
        self::assertMatchesRegularExpression('/\w+/', $token->getFingerprint());
        self::assertEquals($request->getExpMonth(), $token->getExpMonth());
        self::assertEquals($request->getExpYear(), $token->getExpYear());
        self::assertMatchesRegularExpression('/\w+/', $token->getType());
        self::assertMatchesRegularExpression('/\w+/', $token->getBrand());
        self::assertEquals($request->getCardholderName(), $token->getCardholderName());
        if ($token->getUsed()) {
            self::assertCard($request, $token->getCard());
        } else {
            self::assertNull($token->getCard());
        }
        self::assertEquals($request->getAddressLine1(), $token->getAddressLine1());
        self::assertEquals($request->getAddressLine2(), $token->getAddressLine2());
        self::assertEquals($request->getAddressCity(), $token->getAddressCity());
        self::assertEquals($request->getAddressZip(), $token->getAddressZip());
        self::assertEquals($request->getAddressState(), $token->getAddressState());
        self::assertEquals($request->getAddressCountry(), $token->getAddressCountry());
    
        self::assertFraudCheckData($request->getFraudCheckData(), $token->getFraudCheckData());
    }

    /**
     * @param \Shift4\Request\FraudCheckDataRequest $request            
     * @param \Shift4\Response\FraudCheckData $fraudCheckData            
     */
    public static function assertFraudCheckData($request, $fraudCheckData)
    {
        self::assertEquals($request->getIpAddress(), $fraudCheckData->getIpAddress());
        self::assertEquals($request->getEmail(), $fraudCheckData->getEmail());
        self::assertEquals($request->getUserAgent(), $fraudCheckData->getUserAgent());
        self::assertEquals($request->getAcceptLanguage(), $fraudCheckData->getAcceptLanguage());
    }
    
    /**
     * @param \Shift4\Request\SubscriptionRequest $request            
     * @param \Shift4\Response\Subscription $subscription            
     */
    public static function assertSubscription($request, $subscription) 
    {
        self::assertNotNull($subscription->getId());
        self::assertNotNull($subscription->getCreated());
        self::assertFalse($subscription->getDeleted());
        
        self::assertNotNull($subscription->getPlanId());
        self::assertNotNull($subscription->getCustomerId());

        self::assertEquals($request->getQuantity(), $subscription->getQuantity());
        self::assertEquals($request->getCaptureCharges(), $subscription->getCaptureCharges());
        self::assertMatchesRegularExpression('/\w+/', $subscription->getStatus());
        self::assertNotNull($subscription->getRemainingBillingCycles());
        self::assertNotNull($subscription->getStart());
        self::assertNotNull($subscription->getCurrentPeriodStart());
        self::assertNotNull($subscription->getCurrentPeriodEnd());
        self::assertNotNull($subscription->getTrialStart());
        self::assertNotNull($subscription->getTrialEnd());
        self::assertEquals($request->getMetadata(), $subscription->getMetadata());
    }
    
    /**
     * @param \Shift4\Request\PlanRequest $request
     * @param \Shift4\Response\Plan $plan
     */
    public static function assertPlan($request, $plan)
    {
        self::assertNotNull($plan->getId());
        self::assertNotNull($plan->getCreated());
        self::assertFalse($plan->getDeleted());

        self::assertEquals($request->getAmount(), $plan->getAmount());
        self::assertEquals($request->getCurrency(), $plan->getCurrency());
        self::assertEquals($request->getInterval(), $plan->getInterval());
        self::assertEquals($request->getIntervalCount(), $plan->getIntervalCount());
        self::assertEquals($request->getBillingCycles(), $plan->getBillingCycles());
        self::assertEquals($request->getTrialPeriodDays(), $plan->getTrialPeriodDays());
        self::assertEquals($request->getRecursTo(), $plan->getRecursTo());
        self::assertEquals($request->getStatementDescription(), $plan->getStatementDescription());
        self::assertEquals($request->getMetadata(), $plan->getMetadata());
    }
    
    /**
     * @param \Shift4\Request\ChargeRequest $chargeRequest
     * @param \Shift4\Response\Event $event
     */
    public static function assertChargeSucceededEvent($chargeRequest, $event)
    {
        self::assertNotNull($event->getId());
        self::assertNotNull($event->getCreated());
        self::assertEquals('CHARGE_SUCCEEDED', $event->getType());
        self::assertNotNull($event->getLog());
        
        self::assertTrue($event->getData() instanceof \Shift4\Response\Charge);
        self::assertCharge($chargeRequest, $event->getData());
    }
    
    /**
     * @param \Shift4\Request\BlacklistRuleRequest $request
     * @param \Shift4\Response\BlacklistRule $blacklistRule
     */
    public static function assertBlacklistRule($request, $blacklistRule)
    {
        self::assertNotNull($blacklistRule->getId());
        self::assertNotNull($blacklistRule->getCreated());
        self::assertFalse($blacklistRule->getDeleted());
        
        self::assertEquals($request->getRuleType(), $blacklistRule->getRuleType());
        self::assertEquals($request->getFingerprint(), $blacklistRule->getFingerprint());
        self::assertEquals($request->getIpAddress(), $blacklistRule->getIpAddress());
        self::assertEquals($request->getIpCountry(), $blacklistRule->getIpCountry());
        self::assertEquals($request->getMetadataKey(), $blacklistRule->getMetadataKey());
        self::assertEquals($request->getMetadataValue(), $blacklistRule->getMetadataValue());
        self::assertEquals($request->getEmail(), $blacklistRule->getEmail());
        self::assertEquals($request->getUserAgent(), $blacklistRule->getUserAgent());
        self::assertEquals($request->getAcceptLanguage(), $blacklistRule->getAcceptLanguage());
    }

    public static function assertValidCheckoutRequest($signedCheckoutRequest)
    {
        $checkoutUrl = BACKOFFICE_URL . '/checkout?key=' . PUBLIC_KEY . '&checkoutRequest=' . $signedCheckoutRequest;
        $checkoutPage = file_get_contents($checkoutUrl);
        
        $hasError = (strpos($checkoutPage, 'data-error-message') !== false);
        $error = substr($checkoutPage, strpos($checkoutPage, 'data-error-message'));
        $error = substr($error, 0, strpos($error, "\">") + 1);
        self::assertFalse($hasError, "Error for checkout url $checkoutUrl: $error");
        
        $hasInput = (strpos($checkoutPage, 'Shift4Checkout.open(') !== false);
        self::assertTrue($hasInput);
    }

    /**
     * @param \Shift4\Request\CreditRequest $request
     * @param \Shift4\Response\Credit $credit
     */
    public static function assertCredit($request, $credit)
    {
        self::assertNotNull($credit->getId());
        self::assertNotNull($credit->getCreated());
        
        self::assertEquals($request->getAmount(), $credit->getAmount());
        self::assertEquals($request->getCurrency(), $credit->getCurrency());
        self::assertEquals($request->getDescription(), $credit->getDescription());
        self::assertEquals($request->getCustomerId(), $credit->getCustomerId());
        self::assertEquals($request->getMetadata(), $credit->getMetadata());

        self::assertTrue($credit->getFast());
        
        self::assertCard($request->getCard(), $credit->getCard());
        
        Assert::assertNull($credit->getCard()->getFastCredit());
    }

    /**
     * @param string $file
     * @param \Shift4\Response\FileUpload $fileUpload
     */
    public static function assertFileUpload($file, $fileUpload)
    {
        self::assertNotNull($fileUpload->getId());
        self::assertNotNull($fileUpload->getCreated());
        self::assertEquals('dispute_evidence', $fileUpload->getPurpose());
        
        self::assertEquals(filesize($file), $fileUpload->getSize());
        self::assertEquals(pathinfo($file, PATHINFO_EXTENSION), $fileUpload->getType());
        
        self::assertNotNull($fileUpload->getUrl());
    }
    
    /**
     * @param \Shift4\Response\Dispute $expected
     * @param \Shift4\Response\Dispute $dispute
     */
    public static function assertDispute($expected, $dispute)
    {
        self::assertEquals($expected->getId(), $dispute->getId());
        self::assertEquals($expected->getCreated(), $dispute->getCreated());
        self::assertEquals($expected->getUpdated(), $dispute->getUpdated());
        self::assertEquals($expected->getAmount(), $dispute->getAmount());
        self::assertEquals($expected->getCurrency(), $dispute->getCurrency());
        self::assertEquals($expected->getStatus(), $dispute->getStatus());
        self::assertEquals($expected->getReason(), $dispute->getReason());
        self::assertEquals($expected->getAcceptedAsLost(), $dispute->getAcceptedAsLost());

        self::assertEquals($expected->getEvidenceDetails()->getDueBy(), $dispute->getEvidenceDetails()->getDueBy());
        self::assertEquals($expected->getEvidenceDetails()->getHasEvidence(), $dispute->getEvidenceDetails()->getHasEvidence());
        self::assertEquals($expected->getEvidenceDetails()->getPastDue(), $dispute->getEvidenceDetails()->getPastDue());
        self::assertEquals($expected->getEvidenceDetails()->getSubmissionCount(), $dispute->getEvidenceDetails()->getSubmissionCount());
        
        self::assertNotNull($dispute->getCharge());
        self::assertNotNull($dispute->getCharge()->getId());
    }
    
    /**
     * @param \Shift4\Request\DisputeEvidenceRequest $request
     * @param \Shift4\Response\DisputeEvidence $evidence
     */
    public static function assertDisputeEvidence($request, $evidence)
    {
        self::assertEquals($request->getProductDescription(), $evidence->getProductDescription());
        self::assertEquals($request->getCustomerEmail(), $evidence->getCustomerEmail());
        self::assertEquals($request->getCustomerPurchaseIp(), $evidence->getCustomerPurchaseIp());
        self::assertEquals($request->getCustomerSignature(), $evidence->getCustomerSignature());
        self::assertEquals($request->getBillingAddress(), $evidence->getBillingAddress());
        self::assertEquals($request->getReceipt(), $evidence->getReceipt());
        self::assertEquals($request->getCustomerCommunication(), $evidence->getCustomerCommunication());
        self::assertEquals($request->getServiceDate(), $evidence->getServiceDate());
        self::assertEquals($request->getServiceDocumentation(), $evidence->getServiceDocumentation());
        self::assertEquals($request->getDuplicateChargeId(), $evidence->getDuplicateChargeId());
        self::assertEquals($request->getDuplicateChargeDocumentation(), $evidence->getDuplicateChargeDocumentation());
        self::assertEquals($request->getDuplicateChargeExplanation(), $evidence->getDuplicateChargeExplanation());
        self::assertEquals($request->getRefundPolicy(), $evidence->getRefundPolicy());
        self::assertEquals($request->getRefundPolicyDisclosure(), $evidence->getRefundPolicyDisclosure());
        self::assertEquals($request->getRefundRefusalExplanation(), $evidence->getRefundRefusalExplanation());
        self::assertEquals($request->getCancellationPolicy(), $evidence->getCancellationPolicy());
        self::assertEquals($request->getCancellationPolicyDisclosure(), $evidence->getCancellationPolicyDisclosure());
        self::assertEquals($request->getCancellationRefusalExplanation(), $evidence->getCancellationRefusalExplanation());
        self::assertEquals($request->getAccessActivityLogs(), $evidence->getAccessActivityLogs());
        self::assertEquals($request->getShippingAddress(), $evidence->getShippingAddress());
        self::assertEquals($request->getShippingDate(), $evidence->getShippingDate());
        self::assertEquals($request->getShippingCarrier(), $evidence->getShippingCarrier());
        self::assertEquals($request->getShippingTrackingNumber(), $evidence->getShippingTrackingNumber());
        self::assertEquals($request->getShippingDocumentation(), $evidence->getShippingDocumentation());
        self::assertEquals($request->getUncategorizedText(), $evidence->getUncategorizedText());
        self::assertEquals($request->getUncategorizedFile(), $evidence->getUncategorizedFile());
    }
    
    /**
     * @param \Shift4\Response\FraudWarning $expected
     * @param \Shift4\Response\FraudWarning $fraudWarning
     */
    public static function assertFraudWarning($expected, $fraudWarning)
    {
        self::assertEquals($expected->getId(), $fraudWarning->getId());
        self::assertEquals($expected->getCreated(), $fraudWarning->getCreated());
        self::assertEquals($expected->getCharge(), $fraudWarning->getCharge());
        self::assertEquals($expected->getActionable(), $fraudWarning->getActionable());
    }
    
    /**
     * @param \Shift4\Request\ShippingRequest $request
     * @param \Shift4\Response\Shipping $shipping
     */
    private static function assertShipping($request, $shipping)
    {
        if (!$request) {
            self::assertNull($shipping);
            return;
        }
        
        self::assertEquals($request->getName(), $shipping->getName());
    }

    /**
     * @param \Shift4\Request\BillingRequest $request
     * @param \Shift4\Response\Billing $billing
     */
    private static function assertBilling($request, $billing)
    {
        if (!$request) {
            self::assertNull($billing);
            return;
        }
    
        self::assertEquals($request->getName(), $billing->getName());
        self::assertEquals($request->getVat(), $billing->getVat());
    }

    /**
     * @param \Shift4\Request\AddressRequest $request
     * @param \Shift4\Response\Address $address
     */
    private static function assertAddress($request, $address)
    {
        if (!$request) {
            self::assertNull($address);
            return;
        }

        self::assertEquals($request->getLine1(), $address->getLine1());
        self::assertEquals($request->getLine2(), $address->getLine2());
        self::assertEquals($request->getCity(), $address->getCity());
        self::assertEquals($request->getZip(), $address->getZip());
        self::assertEquals($request->getState(), $address->getState());
        self::assertEquals($request->getCountry(), $address->getCountry());
    }
    
    /**
     * @param \Shift4\Response\Payout $payout
     */
    public static function assertPayout($payout)
    {
        self::assertNotNull($payout->getId());
        self::assertNotNull($payout->getCreated());
        self::assertGreaterThan(0, $payout->getAmount());
        self::assertNotNull($payout->getCurrency());
        self::assertNotNull($payout->getPeriodStart());
        self::assertNotNull($payout->getPeriodEnd());
    }
    
    /**
     * @param \Shift4\Response\PayoutTransaction $payoutTransaction
     * @param \Shift4\Response\Payout $payout
     */
    public static function assertPayoutTransaction($payoutTransaction, $payout)
    {
        self::assertNotNull($payoutTransaction->getId());
        self::assertNotNull($payoutTransaction->getCreated());
        
        self::assertNotNull($payoutTransaction->getType());
        self::assertNotNull($payoutTransaction->getAmount());
        self::assertNotNull($payoutTransaction->getCurrency());
        self::assertNotNull($payoutTransaction->getFee());
        
        self::assertEquals($payout->getId(), $payoutTransaction->getPayout());
    }
    
    /**
     * @return \Shift4\Exception\Shift4Exception
     */
    public static function catchShift4Exception($function)
    {
        try {
            $function();
            
        } catch (\Shift4\Exception\Shift4Exception $e) {
            return $e;
        }
        
        self::fail('Shift4Exception expected');
    }
}
