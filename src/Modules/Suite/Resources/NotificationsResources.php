<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Suite\Resources;

use Ometra\Apollo\Sdk\Core\Http\ApolloHttpClient;
use Ometra\Apollo\Sdk\Modules\Suite\Resources\ApplicationsResource;

final class NotificationsResources
{
    public function __construct(private readonly ApolloHttpClient $client) {}

      /**
     * Send a notification using the Apollo notification suite.
     *
     * @param array{users: array, groups: array, title: string, description: string, excluded:string} $notificationData
     * @return void
    */
    public function send(array $notificationData): void
    {
        $this->client->userRequest('POST', 'notifications', $notificationData);
    }
    /**
     * Mark a notification as read using the Apollo notification suite.
     *
     * @param int $id_notification The ID of the notification to mark as read.
     * @return void
     */
    public function read(int $id_notification): void
    {
        $this->client->userRequest('POST', "notifications/{$id_notification}/read");
    }

     public function readAll(): void
    {
        $this->client->userRequest('POST', "notifications/read-all");
    }

     public function index(?int $limit = null): array
    {
        return $this->client->userRequest(
            'GET',
            'notifications',
            $limit === null ? [] : ['limit' => $limit],
        );
    }
}
