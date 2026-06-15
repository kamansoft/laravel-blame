<?php

namespace Kamansoft\LaravelBlame\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class CentralUser extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'name',
    ];

    public function getAuthIdentifierName(): string
    {
        return 'uuid';
    }

    public function getAuthIdentifier()
    {
        return $this->uuid;
    }

    public function getKey()
    {
        return $this->uuid;
    }
}
