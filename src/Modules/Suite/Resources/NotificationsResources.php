<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;

final class NotificationsResources
{
    public function __construct(private readonly ApolloHttpClient $client) {}

    /**
     * Send a notification using the Apollo notification suite.
     *
     * @param  array<string, mixed>  $notificationData
     */
    public function send(array $notificationData): void
    {
        $this->client->userRequest('POST', 'notifications', $notificationData);
    }

    /**
     * Mark a notification as read using the Apollo notification suite.
     *
     * @param  int  $id_notification  The ID of the notification to mark as read.
     */
    public function read(int $id_notification): void
    {
        $this->client->userRequest('POST', "notifications/{$id_notification}/read");
    }

    public function readAll(): void
    {
        $this->client->userRequest('POST', 'notifications/read-all');
    }

    /**
     * Retrieve notifications using the supplied query parameters.
     *
     * @param  array{filter?: mixed, unread?: mixed, items_per_page?: mixed, page?: mixed}  $query
     * @return array{status: int, message: string, data: mixed, errors: array<int|string, mixed>}
     */
    public function index(array $query = []): array
    {
        return $this->client->userRequest(
            'GET',
            'notifications',
            query: $query,
        );
    }
}
