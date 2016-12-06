<?php
/**
 * This file is part of naptime-server.
 *
 * Created by HyanCat.
 *
 * Copyright (C) HyanCat. All rights reserved.
 */
namespace HyanCat\SendCloud;

use Swift_Mime_Message;

/**
 * Given a basic interface for SendCloud api.
 * Interface SendCloudInterface
 * @namespace HyanCat\SendCloud
 */
interface SendCloudInterface
{
    const API_MAIL_SEND = 'http://api.sendcloud.net/apiv2/mail/send';
    const API_MAIL_SEND_TEMPLATE = 'http://api.sendcloud.net/apiv2/mail/sendtemplate';

    /**
     * Send an email with a swift message.
     * @param Swift_Mime_Message $message
     * @return string  The response message from SendCloud.
     */
    public function send(Swift_Mime_Message $message);

    /**
     * Send an email through SendCloud template.
     * @param string $template
     * @param array  $data
     * @return string The response message from SendCloud.
     */
    public function sendTemplate(string $template, array $data = []);
}
