<?php
/**
 * This file is part of HyanCat/SendCloud.
 *
 * Created by HyanCat.
 *
 * Copyright (C) HyanCat. All rights reserved.
 */
namespace HyanCat\SendCloud;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_Message;

/**
 * Class SendCloudTransport
 * @namespace HyanCat\SendCloud
 */
class SendCloudTransport extends Transport
{
    protected $sendCloud;

    public function __construct(SendCloudInterface $sendCloud)
    {
        $this->sendCloud = $sendCloud;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);
        $response = $this->sendCloud->send($message);
        $this->sendPerformed($message);

        $response = json_decode($response, true);
        if ($response['statusCode'] === 200 && $response['result'] === true) {
            return $this->numberOfRecipients($message);
        } else {
            return 0;
        }
    }
}
