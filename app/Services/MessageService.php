<?php

namespace App\Services;

use App\Entities\Message;
use App\Repositories\MessagesRepositoryInterface;
use Carbon\Carbon;
use DomainException;

class MessageService
{
    /**
     * @var MessagesRepositoryInterface
     */
    private MessagesRepositoryInterface $repository;

    public function __construct(MessagesRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(string $body, ?string $parentGuid): Message
    {
        $lastMessage = $this->repository->getLast();

        if ($lastMessage && $lastMessage->getCreatedAt()->diff(Carbon::now())->s <= 10) {
            throw new DomainException('Add new messages not available now');
        }

        $message = new Message($body);

        if ($parentGuid === null) {
            $this->repository->save($message);
        } else {
            $parent = $this->repository->findByGuid($parentGuid);

            if ($parent === null) {
                throw new DomainException('Parent not found');
            }

            $parent->addChild($message);
            $this->repository->save($parent);
        }

        return $message;
    }

    public function update(Message $message, string $body): void
    {
        $message->setMessage($body);
        $this->repository->save($message);
    }
}
