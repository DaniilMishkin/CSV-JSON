<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_name' => $this->document_name,
            'original_filename' => $this->original_filename,
            'is_private' => $this->is_private,
            'records_count' => $this->records_count,
            'status' => $this->status,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at,
            'author' => $this->when(!$this->is_private, function () {
                return [
                    'id' => $this->user?->id,
                    'name' => $this->user?->name,
                ];
            }),
        ];
    }
}
