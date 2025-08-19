<?php

namespace App\Policies;

use App\Enums\ConversionStatuses;
use App\Models\Upload;
use App\Models\User;

class UploadPolicy
{
    public function view(User $user, Upload $upload): bool
    {
        return $upload->is_private === false || $upload->user_id === $user->id;
    }

    public function download(User $user, Upload $upload): bool
    {
        return $this->view($user, $upload) && $upload->status === ConversionStatuses::Done;
    }
}
