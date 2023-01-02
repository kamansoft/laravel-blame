<?php

namespace Kamansoft\LaravelBlame\Traits;

use Illuminate\Support\Facades\Auth;

trait ModelBlamer
{

    use UserModelForAuth;



    protected static function bootModelBlamer()
    {

        static::creating(function ($model) {
            $model->blameOnCreate();
        });
        static::updating(function ($model) {
            $model->blameOnUpdate();
        });
    }

    public function blameOnCreate()
    {
        $creator_field_name = config('blame.created_by_field_name');
        $updater_field_name = config('blame.updated_by_field_name');
        $this->$creator_field_name = $this->$updater_field_name = $this->getUserToBlamePk();
    }

    public function getUserToBlamePk(): string
    {
        $to_return = '';

        if (Auth::check()) {
            $to_return = Auth::user()->getKey();
        } else {
            $to_return = config('blame.system_user_id');
        }
        return $to_return;
    }

    public function blameOnUpdate()
    {
        $updater_field_name = config('blame.updated_by_field_name');
        $this->$updater_field_name = $this->getUserToBlamePk();
    }

    public function creator(): BelongsTo
    {

        return $this->belongsTo(config('auth.providers.users.model'),  config('blame.created_by_field_name'), $this->getUsersModelPkName());
    }


    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('blame.updated_by_field_name'), $this->getUsersModelPkName());
    }


}
