<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    private const CHUNK_DIR = 'chunks';
    private const UPLOAD_DIR = 'uploads';

    private function storage()
    {
        return Storage::disk('public');
    }

    /**
     * 初始化上传
     * @param Request $request
     * @param string $filename 文件名
     * @param int $size 文件大小
     * @param int $chunkSize 分片大小
     * @return JsonResponse
     */
    public function init(Request $request): JsonResponse
    {
        $request->validate([
            'filename' => 'required|string',
            'size' => 'required|integer|min:0',
            'chunkSize' => 'required|integer|min:1',
        ]);

        $filename = $request->input('filename');
        $size = $request->input('size');
        $chunkSize = $request->input('chunkSize');

        $uploadId = Str::uuid()->toString();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $originalName = pathinfo($filename, PATHINFO_FILENAME);
        $uniqueFilename = $originalName . '_' . time() . '.' . $extension;

        $totalChunks = (int)ceil($size / $chunkSize);

        \DB::beginTransaction();

        try {
            $fileRecord = File::create([
                'user_id' => $request->user()->id,
                'original_name' => $filename,
                'storage_name' => $uniqueFilename,
                'path' => self::UPLOAD_DIR . '/' . $uniqueFilename,
                'url' => '',
                'size' => $size,
                'mime_type' => null,
                'extension' => $extension,
                'status' => 'uploading',
            ]);

            \DB::commit();

            return response()->json([
                'message' => '初始化成功',
                'data' => [
                    'uploadId' => $uploadId,
                    'filename' => $uniqueFilename,
                    'totalChunks' => $totalChunks,
                    'fileId' => $fileRecord->id,
                ],
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Init upload failed: ' . $e->getMessage());
            return response()->json([
                'message' => '初始化失败',
            ], 500);
        }
    }

    /**
     * 上传分片
     * @param Request $request
     * @param string $uploadId 上传ID
     * @param int $chunkIndex 分片索引
     * @param int $totalChunks 总分片数
     * @param File $file 分片文件
     * @return JsonResponse
     */
    public function uploadChunk(Request $request): JsonResponse
    {
        $request->validate([
            'uploadId' => 'required|uuid',
            'chunkIndex' => 'required|integer|min:0',
            'totalChunks' => 'required|integer|min:1',
            'file' => 'required|file',
        ]);

        $uploadId = $request->input('uploadId');
        $chunkIndex = $request->input('chunkIndex');

        $chunkFile = $request->file('file');

        $chunkPath = self::CHUNK_DIR . '/' . $uploadId;
        $chunkFilename = $chunkIndex . '.part';
        if (!$this->storage()->exists($chunkPath)) {
            $this->storage()->makeDirectory($chunkPath, 0755, true);
        }

        $this->storage()->putFileAs($chunkPath, $chunkFile, $chunkFilename);

        return response()->json([
            'message' => '分片上传成功',
            'data' => [
                'uploadId' => $uploadId, // 上传ID
                'chunkIndex' => $chunkIndex, // 分片索引
            ],
        ]);
    }

    /**
     * 合并分片
     * @param string $uploadId 上传ID
     * @param string $filename 唯一文件名
     * @param int $totalChunks 总分片数
     * @return JsonResponse
     */
    public function mergeChunks(Request $request): JsonResponse
    {
        $request->validate([
            'uploadId' => 'required|uuid',
            'filename' => 'required|string',
            'totalChunks' => 'required|integer|min:1',
        ]);

        $uploadId = $request->input('uploadId');
        $filename = $request->input('filename');
        $totalChunks = $request->input('totalChunks');

        // 尝试获取文件锁
        if (!$this->acquireLock($uploadId)) {
            return response()->json([
                'message' => '文件正在合并中，请稍后重试',
            ], 409);
        }

        // 使用数据库事务
        \DB::beginTransaction();

        try {
            // 使用悲观锁获取文件记录
            $fileRecord = File::where('storage_name', $filename)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->first();

            if (!$fileRecord) {
                \DB::rollBack();
                $this->releaseLock($uploadId);
                return response()->json([
                    'message' => '文件记录不存在',
                ], 404);
            }

            // 检查文件状态，防止重复合并
            if ($fileRecord->status == 'completed') {
                \DB::rollBack();
                $this->releaseLock($uploadId);
                return response()->json([
                    'message' => '文件已合并完成',
                    'data' => [
                        'filename' => $filename,
                        'url' => $fileRecord->url,
                        'fileId' => $fileRecord->id,
                    ],
                ]);
            }

            $chunkPath = self::CHUNK_DIR . '/' . $uploadId;

            if (!$this->storage()->exists($chunkPath)) {
                \DB::rollBack();
                $this->releaseLock($uploadId);
                return response()->json([
                    'message' => '分片目录不存在',
                ], 400);
            }

            $uploadPath = self::UPLOAD_DIR . '/' . $filename;

            if (!$this->storage()->exists(self::UPLOAD_DIR)) {
                $this->storage()->makeDirectory(self::UPLOAD_DIR);
            }

            $mergedFile = fopen($this->storage()->path($uploadPath), 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkFilePath = $chunkPath . '/' . $i . '.part';

                if (!$this->storage()->exists($chunkFilePath)) {
                    fclose($mergedFile);
                    $this->storage()->delete($uploadPath);
                    \DB::rollBack();
                    $this->releaseLock($uploadId);
                    return response()->json([
                        'message' => '分片 ' . $i . ' 不存在',
                    ], 400);
                }

                $chunkContent = $this->storage()->get($chunkFilePath);
                fwrite($mergedFile, $chunkContent);
            }

            fclose($mergedFile);

            // 删除临时分片目录
            if (!$this->storage()->deleteDirectory($chunkPath)) {
                \Log::warning('Failed to delete chunk directory: ' . $chunkPath);
            }

            $fileUrl = $this->storage()->url($uploadPath);

            // 更新数据库记录
            $fileRecord->update([
                'url' => $fileUrl,
                'status' => 'completed',
            ]);

            // 提交事务
            \DB::commit();
            $this->releaseLock($uploadId);

            return response()->json([
                'message' => '合并成功',
                'data' => [
                    'filename' => $filename,
                    'url' => $fileUrl,
                    'fileId' => $fileRecord->id,
                ],
            ]);

        } catch (\Exception $e) {
            // 回滚事务
            \DB::rollBack();
            $this->releaseLock($uploadId);
            \Log::error('Merge chunks failed: ' . $e->getMessage());
            return response()->json([
                'message' => '合并失败',
            ], 500);
        }
    }

    /**
     * 获取文件锁
     */
    private function acquireLock($uploadId): bool
    {
        $lockPath = self::CHUNK_DIR . '/' . $uploadId . '.lock';

        if ($this->storage()->exists($lockPath)) {
            return false;
        }

        $this->storage()->put($lockPath, 'locked');
        return true;
    }

    /**
     * 释放文件锁
     */
    private function releaseLock($uploadId): void
    {
        $lockPath = self::CHUNK_DIR . '/' . $uploadId . '.lock';
        $this->storage()->delete($lockPath);
    }

    // 检查已上传分片
    public function checkChunks(Request $request): JsonResponse
    {
        $request->validate([
            'uploadId' => 'required|uuid',
            'totalChunks' => 'required|integer|min:1',
        ]);

        $uploadId = $request->input('uploadId');
        $totalChunks = $request->input('totalChunks');

        $chunkPath = self::CHUNK_DIR . '/' . $uploadId;

        if (!$this->storage()->exists($chunkPath)) {
            return response()->json([
                'message' => '检查完成',
                'data' => [
                    'uploadId' => $uploadId,
                    'uploadedChunks' => [],
                ],
            ]);
        }

        $uploadedChunks = [];

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFilePath = $chunkPath . '/' . $i . '.part';
            if ($this->storage()->exists($chunkFilePath)) {
                $uploadedChunks[] = $i;
            }
        }

        return response()->json([
            'message' => '检查完成',
            'data' => [
                'uploadId' => $uploadId,
                'uploadedChunks' => $uploadedChunks,
            ],
        ]);
    }

    // 获取文件列表
    public function listFiles(Request $request): JsonResponse
    {
        $files = File::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => '获取成功',
            'data' => $files,
        ]);
    }

    // 获取单个文件
    public function getFile(Request $request, $id): JsonResponse
    {
        $file = File::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$file) {
            return response()->json([
                'message' => '文件不存在',
            ], 404);
        }

        return response()->json([
            'message' => '获取成功',
            'data' => $file,
        ]);
    }

    // 删除文件
    public function deleteFile(Request $request, $id): JsonResponse
    {
        $file = File::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$file) {
            return response()->json([
                'message' => '文件不存在',
            ], 404);
        }

        $this->storage()->delete($file->path);

        $file->delete();

        return response()->json([
            'message' => '删除成功',
        ]);
    }
}
