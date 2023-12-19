<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachmentRequest;
use App\Models\Attachment;
use App\Http\Resources\AttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class AttachmentController extends Controller
{
    /**
     * @OA\Get(
     *      path="/attachment",
     *      tags={"Attachment"},
     *      summary="List of Attachment",
     *
     *      @OA\Parameter(in="query", required=false, name="filter[original_name]", @OA\Schema(type="string"), example="original_name"),
     *      @OA\Parameter(in="query", required=false, name="filter[keyword]", @OA\Schema(type="string"), example="keyword"),
     *      @OA\Parameter(in="query", required=false, name="sort", @OA\Schema(type="string"), example="original_name"),
     *      @OA\Parameter(in="query", required=false, name="page", @OA\Schema(type="string"), example="1"),
     *      @OA\Parameter(in="query", required=false, name="rows", @OA\Schema(type="string"), example="10"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $rows = 5;
        if ($request->filled('rows')) {
            $rows = $request->rows;
        }

        $perPage = $request->query('per_page', $rows);

        $attachments = QueryBuilder::for(Attachment::class)
            ->allowedFilters([AllowedFilter::callback('keyword', fn (Builder $query, $value) => $query->where('original_name', 'like', '%' . $value . '%')), AllowedFilter::exact('id'), 'original_name'])
            ->allowedSorts('original_name', 'created_at')
            ->paginate($perPage)
            ->appends($request->query());

        return AttachmentResource::collection($attachments);
    }

    /**
     * @OA\Post(
     *      path="/attachment",
     *      tags={"Attachment"},
     *      summary="Store Attachment",
     *
     *      @OA\RequestBody(
     *          description="Body",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="service_name", type="string"),
     *                  @OA\Property(property="file[]", type="file", description="field file ini bertype array of file upload"),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses disimpan."),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *          @OA\JsonContent(
     *              @OA\Property(property="service_name", type="array", @OA\Items(example={"service_name field is required."})),
     *              @OA\Property(property="errors", type="array", @OA\Items(example={"file"="the file field is required"})),
     *          ),
     *      ),
     * )
     */
    public function store(AttachmentRequest $request)
    {
        $validated = $request->validated();
        $file = $validated['file'];

        $storage = app('firebase.storage');
        $storagePath = 'product/' . $file->hashName();

        $firebaseFile = $storage->getBucket()->upload(file_get_contents($file->getRealPath()), ['name' => $storagePath]);

        Attachment::create([
            'hash_name' => $firebaseFile->name(),
            'original_name' => $file->getClientOriginalName(),
            'size' => round($file->getSize() / 1024, 2),
            'mimetypes' => $file->getMimeType(),
            'service_name' => 'firebase',
        ]);

        return $this->sendSuccess([], 'Data berhasil disimpan.', 201);
    }

    /**
     * @OA\Get(
     *      path="/attachment/{id}",
     *      tags={"Attachment"},
     *      summary="Attachment details",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Attachment ID"),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(Attachment $attachment)
    {
        return $this->sendSuccess(new AttachmentResource($attachment), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Post(
     *      path="/attachment/{id}",
     *      tags={"Attachment"},
     *      summary="Update Attachment",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Attachment ID"),
     *      @OA\RequestBody(
     *          description="Body",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="file", type="file"),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses disimpan."),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *          @OA\JsonContent(
     *              @OA\Property(property="file", type="array", @OA\Items(example={"file_name field is required."}))
     *          ),
     *      ),
     * )
     */
    public function update(AttachmentRequest $request, Attachment $attachment)
    {
        $hash_name_old = $attachment->hash_name;
        $validated = $request->validated();
        $file = $validated['file'];

        $storage = app('firebase.storage');
        $storagePath = 'product/' . $file->hashName();

        $firebaseFile = $storage->getBucket()->upload(file_get_contents($file->getRealPath()), ['name' => $storagePath]);

        $attachment->update([
            'hash_name' => $firebaseFile->name(),
            'original_name' => $file->getClientOriginalName(),
            'size' => round($file->getSize() / 1024, 2),
            'mimetypes' => $file->getMimeType(),
        ]);

        //deleted file
        $storage->getBucket()->object($hash_name_old)->delete();

        return $this->sendSuccess(new AttachmentResource($attachment), 'Data berhasil dirubah.', 200);
    }

    /**
     * @OA\Delete(
     *      path="/attachment/{id}",
     *      tags={"Attachment"},
     *      summary="Attachment Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Attachment ID"),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses dihapus."),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="Data tidak ditemukan."),
     *          )
     *      ),
     * )
     */
    public function destroy(Attachment $attachment)
    {
        (app('firebase.storage'))->getBucket()->object($attachment->hash_name)->delete();

        $attachment->delete();
        return $this->sendSuccess([], null, 204);
    }

    /**
     * @OA\Get(
     *      path="/attachment/{id}/download",
     *      tags={"Attachment"},
     *      summary="Download Attachment",
     *
     *      @OA\Parameter(in="path", required=true, name="id", @OA\Schema(type="integer"), description="Attachment ID"),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\MediaType(
     *              mediaType="application/pdf"
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response="404",
     *          description="error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="File tidak ditemukan."),
     *          )
     *      ),
     * )
     */
    public function download(Attachment $attachment)
    {
        $nameFile = $attachment->hash_name;
        $storage = app('firebase.storage');
        $signedUrl = $storage->getBucket()->object($nameFile)->signedUrl(now()->addDay());

        return redirect()->away($signedUrl);
    }
}
