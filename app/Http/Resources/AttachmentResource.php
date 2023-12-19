<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $storage = app('firebase.storage');
        $object = $storage->getBucket()->object($this->hash_name);

        $objectInfo = $object->info();
        if (!isset($objectInfo['metadata']['acl']) || empty($objectInfo['metadata']['acl'])) {
            $object->update(['acl' => []]);
        }

        $url = $object->signedUrl(now()->addDay());

        return [
            'id'            => $this->id,
            'hash_name'     => $this->hash_name,
            'original_name' => $this->original_name,
            'size'          => $this->size,
            'mimetypes'     => $this->mimetypes,
            'service_name'  => $this->service_name,
            'url'           => $url,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
