<?php

class Sitemaster_Checkout_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    /**
     * Send confirmation request email if "is_send_request_email" value equals true
     *
     * @return Sitemaster_Checkout_Model_Subscriber
     */
    public function sendConfirmationRequestEmail()
    {
        if ($this->getIsSendRequestEmail()) {
            return parent::sendConfirmationRequestEmail();
        }
        return $this;
    }

    /**
     * Send confirmation success email if "is_send_success_email" value equals true
     *
     * @return Sitemaster_Checkout_Model_Subscriber
     */
    public function sendConfirmationSuccessEmail()
    {
        if ($this->getIsSendSuccessEmail()) {
            return parent::sendConfirmationRequestEmail();
        }
        return $this;
    }
}
