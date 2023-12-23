<?php

namespace Src\MedicalReference\Dto\Common;

class OID
{
    /**
     * @var string
     */
    private $extension;
    /**
     * @var string
     */
    private $root;

    /**
     * @param string $extension
     * @param string $root
     */
    public function __construct(string $extension, string $root)
    {
        $this->extension = $extension;
        $this->root = $root;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getRoot(): string
    {
        return $this->root;
    }
}
