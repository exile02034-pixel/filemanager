<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileVersion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'file_id',
        'stored_name',
        'path',
        'size',
        'mime_type',
        'version_number',
    ];

   
    public function file()
    {
        return $this->belongsTo(File::class);
    }
}