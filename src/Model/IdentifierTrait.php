<?php

namespace App\Model;

use Ramsey\Uuid\UuidInterface;

/**
 * Contains id and uuid with getters and setters.
 */
trait IdentifierTrait
{
    private string $id;
    private UuidInterface $uuid;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
