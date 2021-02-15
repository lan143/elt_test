<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Message
{
    private string $guid;
    private string $message;
    private Carbon $createdAt;
    private Collection $child;

    public function __construct(string $message)
    {
        $this->guid = Str::uuid();
        $this->message = $message;
        $this->createdAt = Carbon::now();
        $this->child = new Collection();
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @return Collection|self[]
     */
    public function getChild(): Collection
    {
        return $this->child;
    }

    public function addChild(Message $message): void
    {
        $this->child->push($message);
    }
}
