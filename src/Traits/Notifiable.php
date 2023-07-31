<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Auth\Traits;

use Drewlabs\Packages\Auth\NotificationChannels;

trait Notifiable
{
    // #region Account configured channels relations
    public function channels()
    {
        return $this->hasMany(UserNotificationChannel::class, 'user_id', 'user_id');
    }
    // #endregion Account configured channels relations

    public function getChannels()
    {
        return $this->channels ?? [];
    }

    public function addTextMessageChannel(
        $value,
        bool $verified = false,
        bool $default = false
    ) {
        return $this->addChannel(
            $value,
            NotificationChannels::TEXT,
            $verified,
            $default
        );
    }

    public function addMailChannel(
        $value,
        bool $verified = false,
        bool $default = false
    ) {
        return $this->addChannel(
            $value,
            NotificationChannels::MAIL,
            $verified,
            $default
        );
    }

    public function addChannel(
        $value,
        string $channel,
        bool $verified = false,
        bool $default = false
    ) {
        // Add new channel to user notification channels collection
        $this->channels()->updateOrCreate([
            'user_id' => $this->getKey(),
            'channel' => (string) $channel,
        ], [
            'channel' => $channel,
            'identifier' => (string) $value,
            'verified' => $verified,
            'default' => $default,
        ]);

        return $this;
    }
}
