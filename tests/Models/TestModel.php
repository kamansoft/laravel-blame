<?php

namespace Kamansoft\LaravelBlame\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kamansoft\LaravelBlame\Contracts\ModelBlame;
use Kamansoft\LaravelBlame\Tests\Factories\TestModelFactory;
use Kamansoft\LaravelBlame\Traits\ModelBlamer;

class TestModel extends Model implements ModelBlame
{
    use HasFactory;
    use ModelBlamer;

    protected $table = 'test_models';

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];

    protected static function newFactory()
    {
        return TestModelFactory::new();
    }
}
