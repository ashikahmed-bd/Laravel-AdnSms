<?php

namespace Ashik\AdnSms\Abstract;

use Ashik\AdnSms\Exceptions\InvalidMessageFormatException;
use Ashik\AdnSms\Exceptions\InvalidRequestTypeException;
use Ashik\AdnSms\Exceptions\MissingRequiredMethodCallException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

abstract class AdnSmsAbstract
{

    protected bool $enabled;
    protected string $apiUrl;
    protected string $message;
    protected string $sender;
    protected string $format;
    protected string $requestType;
    protected bool $isPromotional;
    protected string $campaignTitle;

    private array $allowedRequestTypes = ['SINGLE_SMS', 'OTP', 'GENERAL_CAMPAIGN', 'MULTIBODY_CAMPAIGN'];
    private array $allowedMessageFormats = ['TEXT', 'UNICODE'];

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $this->setEnabled(config('adn-sms.enabled'));
        $this->setFormat(config('adn-sms.message_format'));
    }

    /**
     * @return bool
     */
    protected function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param $enabled
     * @return boolean
     */
    protected function setEnabled($enabled): bool
    {
        return $this->enabled = $enabled;
    }


    /**
     * @return string
     */
    protected function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     *
     */
    protected function setApiUrl($url): void
    {
        $this->apiUrl = $url;
    }


    protected function getMessageBody(): string
    {
        return $this->message;
    }


    protected function setMessageBody(string $message): void
    {
        $this->message = $message;
    }

    protected function getSender(): string
    {
        return $this->sender;
    }

    /**
     * @param array | string $sender
     */
    protected function setSender(array|string $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @throws Throwable
     */
    protected function getFormat(): string
    {
        throw_if(empty($this->format), new MissingRequiredMethodCallException('Missing required method call format()'));
        return $this->format;
    }

    /**
     * @throws Throwable
     */
    public function setFormat(string $format): string
    {
        throw_if(!in_array(strtoupper($format), $this->allowedMessageFormats), new InvalidMessageFormatException('Invalid message format. Must be one of ' . implode(', ', $this->allowedMessageFormats) . '.'));
        return $this->format = $format;
    }


    /**
     * @throws Throwable
     */
    protected function getRequestType(): string
    {
        throw_if((
                $this->requestType == 'GENERAL_CAMPAIGN' ||
                $this->requestType == 'MULTIBODY_CAMPAIGN'
            )
            && empty($this->campaignTitle),
            new MissingRequiredMethodCallException('Missing required method call campaignTitle()')
        );
        return $this->requestType;
    }

    /**
     * @throws Throwable
     */
    protected function setRequestType(string $requestType): void
    {
        throw_if(!in_array(strtoupper($requestType), $this->allowedRequestTypes), new InvalidRequestTypeException('Invalid request type. Must be one of ' . implode(', ', $this->allowedRequestTypes) . '.'));
        $this->requestType = $requestType;
    }


    public function getPromotional()
    {
        return $this->isPromotional;
    }

    public function setPromotional(bool $isPromotional): void
    {
        $this->isPromotional = $isPromotional;
    }


    protected function getCampaignTitle()
    {
        return $this->campaignTitle;
    }


    protected function setCampaignTitle(string $campaignTitle): void
    {
        $this->campaignTitle = $campaignTitle;
    }

    /**
     * Make request and return response
     *
     * @param array $data
     * @return Response
     * @throws Throwable
     */

    protected function callToApi(array $data = []): Response
    {
        if (!$this->enabled) Http::fake();

        $request = array_merge($data, [
            'api_key' => config('adn-sms.api_key'),
            'api_secret' => config('adn-sms.api_secret'),
        ]);

        return Http::baseUrl(config('adn-sms.base_url'))->post($this->getApiUrl(), $request);
    }

}
