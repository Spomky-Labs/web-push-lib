<?php

declare(strict_types=1);

namespace WebPush;

use Psr\Http\Message\ResponseInterface;

final class StatusReport implements StatusReportInterface
{
    /**
     * @param string[] $links
     */
    public function __construct(
        private readonly SubscriptionInterface $subscription,
        private readonly NotificationInterface $notification,
        private readonly int $code,
        private readonly string $location,
        private readonly array $links
    ) {
    }

    /**
     * @param string[] $links
     */
    public static function create(
        SubscriptionInterface $subscription,
        NotificationInterface $notification,
        int $code,
        string $location,
        array $links
    ): self {
        return new self($subscription, $notification, $code, $location, $links);
    }

    public static function createFromResponse(
        SubscriptionInterface $subscription,
        NotificationInterface $notification,
        ResponseInterface $response
    ): self {
        $code = $response->getStatusCode();
        $headers = $response->getHeaders();
        $location = implode(', ', $headers['location'] ?? ['']);
        $links = $headers['link'] ?? [];

        return new self($subscription, $notification, $code, $location, $links);
    }

    public function getSubscription(): SubscriptionInterface
    {
        return $this->subscription;
    }

    public function getNotification(): NotificationInterface
    {
        return $this->notification;
    }

    public function isSuccess(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    public function isSubscriptionExpired(): bool
    {
        return $this->code === 404 || $this->code === 410;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return string[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
