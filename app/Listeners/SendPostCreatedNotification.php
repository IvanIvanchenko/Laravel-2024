<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Notifications\PostCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PostCreated $event)
    {
        $post = $event->post;
        $author = $post->author;

        if ($author && $author->email) {
            $author->notify(new PostCreatedNotification($post));
        }
    }
}
