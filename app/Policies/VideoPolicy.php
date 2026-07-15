<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_video');
    }

    public function view(User $user, Video $video): bool
    {
        return $user->can('view_video');
    }

    public function create(User $user): bool
    {
        return $user->can('create_video');
    }

    public function update(User $user, Video $video): bool
    {
        return $user->can('update_video');
    }

    public function delete(User $user, Video $video): bool
    {
        return $user->can('delete_video');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_video');
    }
}
