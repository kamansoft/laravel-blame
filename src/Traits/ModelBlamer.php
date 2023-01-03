<?php

namespace Kamansoft\LaravelBlame\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait ModelBlamer
{
    use UserModelForAuth;

    protected static function bootModelBlamer(): void
    {
        static::creating(function ($model) {
            $model->blameOnCreate();
        });
        static::updating(function ($model) {
            $model->blameOnUpdate();
        });
    }

    public function blameOnCreate(): void
    {
        $creator_field_name = config('blame.created_by_field_name');
        $updater_field_name = config('blame.updated_by_field_name');
        $blamed = $this->getUserToBlamePk();
        Log::info(static::class.' on create, blames to user: '.$blamed);
        $this->$creator_field_name = $this->$updater_field_name = $blamed;
    }

    public function getUserToBlamePk(): string
    {
        $to_return = '';
        if (Auth::check()) {
            $to_return = Auth::user()->getKey();
        } else {
            Log::warning(static::class.' Not logged user using system user');
            $to_return = env('BLAME_SYSTEM_USER_ID');
        }

        return $to_return;
    }

    public function blameOnUpdate(): void
    {
        $updater_field_name = config('blame.updated_by_field_name');
        $blamed = $this->getUserToBlamePk();
        Log::info(static::class.' on update, blames to user: '.$blamed);
        $this->$updater_field_name = $blamed;
    }

    /**
     * Relation with the user who created the model.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('blame.created_by_field_name'), $this->getUsersModelPkName());
    }

    /**
     * Relation with the user who last updated the model.
     *
     * @return BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('blame.updated_by_field_name'), $this->getUsersModelPkName());
    }
}
