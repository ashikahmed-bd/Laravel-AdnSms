<?php

namespace Ashik\AdnSms;

use Ashik\AdnSms\Abstract\AdnSmsAbstract;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class AdnSms extends AdnSmsAbstract
{

    /**
     * @throws Throwable
     */
    public function checkBalance(): \Illuminate\Http\Client\Response
    {
        $this->setApiUrl(config('adn-sms.api_url.check_balance'));

        return $this->callToApi();
    }

    /**
     * @param string $sender
     * @return $this
     * @throws Throwable
     */
    public function to(string $sender): static
    {
        $this->setApiUrl(config('adn-sms.api_url.send_sms'));
        $this->setRequestType('SINGLE_SMS');
        $this->sender = $sender;
        return $this;
    }

    /**
     * @param string $sender
     * @return $this
     * @throws Throwable
     */
    public function otp(string $sender): static
    {
        $this->setApiUrl(config('adn-sms.api_url.send_sms'));
        $this->setRequestType('OTP');
        $this->sender = $sender;
        return $this;
    }

    /**
     * @param array $senders
     * @return $this
     * @throws Throwable
     */
    public function bulk(array $senders): static
    {
        $this->setApiUrl(config('adn-sms.api_url.send_sms'));
        $this->setRequestType('GENERAL_CAMPAIGN');
        $this->setSender(implode(',', $senders));
        $this->setPromotional($this->getFormat() == 'TEXT');
        return $this;
    }

    public function message(string $message): static
    {
        $this->setMessageBody($message);

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function format(string $format): static
    {
        $this->setFormat($format);

        return $this;
    }


    public function campaignTitle(string $campaignTitle): static
    {
        $this->setCampaignTitle($campaignTitle);
        return $this;
    }

    /**
     * @throws Throwable
     */
    public function checkCampaignStatus(string $campaign_uid): void
    {
        $this->setApiUrl(config('adn-sms.api_url.check_campaign_status'));
        $data = [
            'campaign_uid' => $campaign_uid,
        ];
        $this->callToApi($data);
    }

    /**
     * @throws Throwable
     */
    public function checkSmsStatus(string $sms_uid): void
    {
        $this->setApiUrl(config('adn-sms.api_url.check_sms_status'));
        $data = [
            'sms_uid' => $sms_uid,
        ];
        $this->callToApi($data);
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    public function send(): void
    {
        $data = [
            'mobile' => $this->getSender(),
            'message_body' => $this->getMessageBody(),
            'request_type' => $this->getRequestType(),
            'message_type' => $this->getFormat(),
        ];

        if ($this->getCampaignTitle()){
            $data['campaign_title'] = $this->getCampaignTitle();
        }

        if ($this->getPromotional()){
            $data['isPromotional'] = $this->getPromotional();
        }

        $this->callToApi($data);
    }

    /**
     * @param $callback
     * @return void
     * @throws ConnectionException
     * @throws Throwable
     */
    public function queue($callback = null): void
    {
        dispatch(function () use ($callback) {
            $response = $this->send();
            if (is_callable($callback)) call_user_func($callback, $response);
        });
    }

}
