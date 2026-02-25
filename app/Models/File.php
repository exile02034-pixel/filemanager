<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'folder_id',
        'original_name',
        'stored_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'year',
        'month',
    ];

 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }


    public function versions()
    {
        return $this->hasMany(FileVersion::class);
    }
}