<?php

namespace Kamansoft\LaravelBlame\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface ModelBlame
{



    public function blameOnCreate(): void;

    public function getUserToBlamePk(): string;

    public function blameOnUpdate(): void;


    public function creator(): BelongsTo;

    public function updater(): BelongsTo;

}
