<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2020-2021 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace WebPush;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class PreferAsyncExtension implements Extension, Loggable
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public static function create(): self
    {
        return new self();
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function process(RequestInterface $request, Notification $notification, Subscription $subscription): RequestInterface
    {
        if (!$notification->isAsync()) {
            $this->logger->debug('Sending synchronous notification');

            return $request;
        }

        $this->logger->debug('Sending asynchronous notification');

        return $request
            ->withAddedHeader('Prefer', 'respond-async')
        ;
    }
}
