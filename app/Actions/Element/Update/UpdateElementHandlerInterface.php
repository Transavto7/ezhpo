<?php

namespace App\Actions\Element\Update;

interface UpdateElementHandlerInterface
{
    public function handle($id, array $data);
}
