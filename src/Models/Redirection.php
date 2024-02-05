<?php

namespace Pardalsalcap\HailoRedirections\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pardalsalcap\HailoRedirections\Database\Factories\RedirectionsFactory;

/**
 * @property int $id
 * @property string $hash
 * @property string $url
 * @property string $fix
 * @property int $http_status
 * @property string $created_at
 * @property string $updated_at
 */
class Redirection extends Model
{
    use HasFactory;
    /**
     * @var array
     */
    protected $fillable = ['hash', 'url', 'fix', 'hits', 'http_status', 'created_at', 'updated_at'];

    protected static function newFactory()
    {
        return new RedirectionsFactory();
}
}
