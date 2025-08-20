<?php

namespace App\Repositories;

use App\Enums\ConversionStatuses;
use App\Http\Requests\PaginatedRequest;
use App\Http\Requests\StoreUploadRequest;
use App\Http\Responses\Upload\UploadListItemResponse;
use App\Models\Upload;
use App\Models\User;
use App\Services\Storage\FileStorageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\PaginatedDataCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadRepository extends AbstractRepository
{
    public function __construct(
        private readonly FileStorageService $fileStorageService
    ) {}

    public function listPaginated(PaginatedRequest $request, string $mapTo = UploadListItemResponse::class): PaginatedDataCollection
    {
        $query = $this->listQuery()
            ->orderBy('id');

        switch ($mapTo) {
            case UploadListItemResponse::class:
                $query->with(['author']);
                break;
        }

        return $this->paginate($query, $request, $mapTo);
    }

    public function create(StoreUploadRequest $request): Upload
    {
        //        if ($this->userHasRecentUpload($this->user())) {
        //            throw ValidationException::withMessages([
        //                'file' => 'Upload limit: 1 file per 5 minutes',
        //            ])->status(429);
        //        }

        if ($this->user()->created_at === null || $this->user()->created_at->lt(now()->subDays(10)) === false) {
            throw ValidationException::withMessages([
                'file' => 'Account must be at least 10 days old',
            ])->status(403);
        }

        $model = new Upload();
        $model->name = $request->name;
        $model->is_private = $request->isPrivate;
        $model->conversion_strategy = $request->strategy;
        $model->author()->associate($this->user());

        $model->save();

        return $model;
    }

    public function markProcessing(Upload $upload): Upload
    {
        $upload->status = ConversionStatuses::Processing;
        $upload->save();

        return $upload;
    }

    public function markDone(Upload $upload, string $jsonPath): Upload
    {
        $upload->status = ConversionStatuses::Done;
        $upload->path_converted = $jsonPath;
        $upload->save();

        return $upload;
    }

    public function markError(Upload $upload, string $error): Upload
    {
        $upload->status = ConversionStatuses::Error;
        $upload->error_message = $error;
        $upload->save();

        return $upload;
    }

    public function download(Upload $upload): StreamedResponse
    {
        $this->checkAccess('download', $upload);

        return $this->fileStorageService->download($upload->path_converted, pathinfo($upload->name, PATHINFO_FILENAME).'.json');
    }

    private function listQuery(): Builder
    {
        $query = Upload::query();

        if (!is_null($this->user())) {
            return $query->where(function (Builder $query) {
                $query->where('user_id', $this->user()->id)
                    ->orWhere('is_private', false);
            });
        }

        return $query->where('is_private', false);
    }

    private function userHasRecentUpload(User $user, int $minutes = 5): bool
    {
        return Upload::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->exists();
    }
}
