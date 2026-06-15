<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kamansoft\LaravelBlame\Contracts\ModelBlame;
use Kamansoft\LaravelBlame\Traits\ModelBlamer;

class BlamedPost extends Model implements ModelBlame
{
    use ModelBlamer;

    protected $fillable = [
        'title',
    ];
}
