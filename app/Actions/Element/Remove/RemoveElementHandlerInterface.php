<?php

namespace App\Actions\Element\Remove;

interface RemoveElementHandlerInterface
{
    public function handle($id, bool $deleting);
}
