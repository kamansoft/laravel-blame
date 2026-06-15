<?php

namespace Kamansoft\LaravelBlame\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Kamansoft\LaravelBlame\BlameUserResolver;

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
        $creatorFieldName = config('blame.created_by_field_name');
        $updaterFieldName = config('blame.updated_by_field_name');
        $blamed = $this->getUserToBlamePk();

        Log::info(static::class.' on create, blames to user: '.$blamed);

        $this->$creatorFieldName = $this->$updaterFieldName = $blamed;
    }

    public function getUserToBlamePk(): string
    {
        return app(BlameUserResolver::class)->resolveId();
    }

    public function blameOnUpdate(): void
    {
        $updaterFieldName = config('blame.updated_by_field_name');
        $blamed = $this->getUserToBlamePk();

        Log::info(static::class.' on update, blames to user: '.$blamed);

        $this->$updaterFieldName = $blamed;
    }

    /**
     * Relation with the user who created the model.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('blame.created_by_field_name'), $this->getUsersModelPkName());
    }

    /**
     * Relation with the user who last updated the model.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('blame.updated_by_field_name'), $this->getUsersModelPkName());
    }
}
