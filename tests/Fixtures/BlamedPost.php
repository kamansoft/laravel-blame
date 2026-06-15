<?php

namespace Kamansoft\LaravelBlame\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Kamansoft\LaravelBlame\Contracts\ModelBlame;
use Kamansoft\LaravelBlame\Traits\ModelBlamer;

class BlamedPost extends Model implements ModelBlame
{
    protected $table = 'blamed_posts';
    use ModelBlamer;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
