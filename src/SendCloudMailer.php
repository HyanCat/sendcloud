<?php
/**
 * This file is part of sendcloud.
 *
 * Created by HyanCat.
 *
 * Copyright (C) HyanCat. All rights reserved.
 */
namespace HyanCat\SendCloud;

use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;
use Swift_Mime_Message;

/**
 * A mailer that implemented SendCloudInterface, this provide a simple implement of SendCloud api.
 * Class SendCloudMailer
 * @namespace HyanCat\SendCloud
 */
class SendCloudMailer implements SendCloudInterface
{
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    public function __construct(Repository $config)
    {
        $this->config = $config;
        $this->client = new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message)
    {
        $options['form_params'] = $this->buildParams($message);

        $response = $this->client->post(self::API_MAIL_SEND, $options);

        return (string)$response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function sendTemplate(string $template, array $data = [])
    {
        // Not implement yet.
    }

    private function buildParams(Swift_Mime_Message $message, array $appendParams = [])
    {
        $apiUser  = $this->config->get('sendcloud.api.user');
        $apiKey   = $this->config->get('sendcloud.api.key');
        $from     = $this->config->get('sendcloud.from.address');
        $fromName = $this->config->get('sendcloud.from.name');
        $param    = [
            'apiUser'     => $apiUser,
            'apiKey'      => $apiKey,
            'from'        => implode(';', array_keys($message->getFrom())) ?: $from,
            'fromName'    => implode(';', array_values($message->getFrom())) ?: $fromName,
            'to'          => implode(';', array_keys($message->getTo())),
            'subject'     => $message->getSubject(),
            'html'        => $message->getBody(),
            'cc'          => implode(';', $message->getCc() ?: []),
            'bcc'         => implode(';', $message->getBcc() ?: []),
            'respEmailId' => 'true',
        ];
        $param    = array_merge($param, $appendParams);

        return $param;
    }
}
