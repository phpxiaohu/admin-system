<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    // 可批量赋值的字段
    protected $fillable = [
        'user_id',           // 用户ID，关联上传文件的用户
        'original_name',     // 原始文件名，用户上传时的文件名
        'storage_name',      // 存储文件名，系统生成的唯一文件名
        'path',              // 文件存储路径（相对路径）
        'url',               // 文件访问URL
        'size',              // 文件大小（字节）
        'mime_type',         // MIME类型（如 image/png）
        'extension',         // 文件扩展名（如 png）
        'status',            // 文件状态：uploading(上传中)、completed(已完成)、failed(失败)
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
