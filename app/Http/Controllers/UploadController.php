<?php

namespace App\Http\Controllers;

use App\Enums\JSONStructures;
use App\Helpers\EnumMapper;
use App\Http\Requests\PaginatedRequest;
use App\Http\Requests\StoreUploadRequest;
use App\Models\Upload;
use App\Repositories\UploadRepository;
use App\Services\UploadService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UploadController extends Controller
{
    public function __construct(
        private readonly UploadRepository $uploadRepository,
        private readonly UploadService $uploadService
    ) {}

    public function list(PaginatedRequest $request): InertiaResponse
    {
        return Inertia::render('uploads/List', [
            'items' => $this->uploadRepository->listPaginated($request),
        ]);
    }

    /**
     * @throws Exception
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('uploads/Create', [
            'options' => [
                'strategyOptions' => EnumMapper::list(JSONStructures::class),
            ],
        ]);
    }

    public function store(StoreUploadRequest $request): RedirectResponse
    {
        $this->uploadService->createAndConvert($request);

        return redirect()->route('uploads.page.list');
    }

    public function download(Upload $upload)
    {
        return $this->uploadRepository->download($upload);
    }
}
