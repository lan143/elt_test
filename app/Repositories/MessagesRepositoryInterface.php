<?php

namespace App\Repositories;

use App\Entities\Message;
use Illuminate\Support\Collection;

interface MessagesRepositoryInterface
{
    /**
     * @return Collection|Message[]
     */
    public function getAll(): Collection;

    public function getLast(): ?Message;

    public function findByGuid(string $guid): ?Message;

    public function save(Message $message): void;
}
