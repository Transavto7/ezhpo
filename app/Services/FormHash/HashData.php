<?php

namespace App\Services\FormHash;


interface HashData
{
    public function toHashString() : string;
}
