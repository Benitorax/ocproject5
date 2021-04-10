<?php

namespace App\Model;

use Ramsey\Uuid\UuidInterface;

/**
 * Contains id and uuid with getters and setters.
 */
trait IdentifierTrait
{
    private int $id;
    private UuidInterface $uuid;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
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
