<?php

namespace App\Actions\Anketa;

use Illuminate\Contracts\Auth\Authenticatable;

interface CreateFormHandlerInterface
{
    public function handle(array $data, Authenticatable $user): array;
}
