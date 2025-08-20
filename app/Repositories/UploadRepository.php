<?php

namespace App\Repositories;

use App\Enums\ConversionStatuses;
use App\Http\Responses\Upload\UploadListItemResponse;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\PaginatedDataCollection;

class UploadRepository extends AbstractRepository
{
    public function getUpload(int $id): Upload
    {
        return Upload::query()->findOrFail($id);
    }

    public function listPaginated(
        int $page,
        int $perPage,
        ?int $userId,
        string $mapTo = UploadListItemResponse::class
    ): PaginatedDataCollection {
        $query = $this->listQuery($userId)
            ->orderBy('id');

        switch ($mapTo) {
            case UploadListItemResponse::class:
                $query->with(['author']);
                break;
        }

        return $this->paginate($query, $page, $perPage, $mapTo);
    }

    public function create(string $name, bool $isPrivate, int $userId): Upload
    {
        $model = new Upload();
        $model->name = $name;
        $model->is_private = $isPrivate;
        $model->author()->associate($userId);

        return $model;
    }

    public function updateStatus(int|Upload $id, ConversionStatuses $status): bool
    {
        $model = $id instanceof Upload ? $id : $this->getUpload($id);
        $model->status = $status;

        return $model->save();
    }

    public function updatePathOriginal(int|Upload $id, string $path): bool
    {
        $model = $id instanceof Upload ? $id : $this->getUpload($id);
        $model->path_original = $path;

        return $model->save();
    }

    public function updatePathConverted(int|Upload $id, string $path): bool
    {
        $model = $id instanceof Upload ? $id : $this->getUpload($id);
        $model->path_converted = $path;

        return $model->save();
    }

    public function updateErrorMessage(int|Upload $id, string $message): bool
    {
        $model = $id instanceof Upload ? $id : $this->getUpload($id);
        $model->error_message = $message;

        return $model->save();
    }

    public function userHasRecentUpload(User $user, int $minutes = 5): bool
    {
        return Upload::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->exists();
    }

    private function listQuery(?int $userId): Builder
    {
        $query = Upload::query();

        if (!is_null($userId)) {
            return $query->where(function (Builder $query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('is_private', false);
            });
        }

        return $query->where('is_private', false);
    }
}
