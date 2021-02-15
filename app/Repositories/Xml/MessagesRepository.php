<?php

namespace App\Repositories\Xml;

use App\Entities\Message;
use App\Repositories\MessagesRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use samdark\hydrator\Hydrator;
use SimpleXMLElement;

class MessagesRepository implements MessagesRepositoryInterface
{
    private SimpleXMLElement $xml;
    private Hydrator $hydrator;
    private array $mapping = [
        'guid' => 'guid',
        'message' => 'message',
        'createdAt' => 'createdAt',
        'child' => 'child',
    ];
    private array $types = [
        'guid' => 'string',
        'message' => 'string',
        'createdAt' => 'carbon',
        'child' => 'collection',
    ];

    public function __construct()
    {
        if (file_exists(storage_path('messages.xml'))) {
            $this->xml = new SimpleXMLElement(file_get_contents(storage_path('messages.xml')));
        } else {
            $this->xml = new SimpleXMLElement('<?xml version=\'1.0\' standalone=\'yes\'?><messages></messages>');
        }

        $this->hydrator = new Hydrator($this->mapping);
    }

    /**
     * @param Message $message
     */
    public function save(Message $message): void
    {
        $found = false;

        foreach ($this->xml as $messageData) {
            if ((string)$messageData->guid === $message->getGuid()) {
                $messageData->message = $message->getMessage();
                $messageData->createdAt = $message->getCreatedAt()->format('d.m.Y H:i:s');
                $found = true;

                foreach ($message->getChild() as $child) {
                    $foundChild = false;

                    foreach ($messageData->childs as $childEl) {
                        if ((string)$childEl->guid === $child->getGuid()) {
                            $childEl->message = $child->getMessage();
                            $foundChild = true;
                            break;
                        }
                    }

                    if (!$foundChild) {
                        if (count($messageData->children('childs')) > 0) {
                            $childsElement = $messageData->childs;
                        } else {
                            $childsElement = $messageData->addChild('childs');
                        }

                        $childElement = $childsElement->addChild('message');
                        $childElement->addChild('guid', $child->getGuid());
                        $childElement->addChild('message', $child->getMessage());
                        $childElement->addChild('createdAt', $child->getCreatedAt()->format('d.m.Y H:i:s'));
                    }
                }
            }
        }

        if (!$found) {
            $messageElement = $this->xml->addChild('message');
            $messageElement->addChild('guid', $message->getGuid());
            $messageElement->addChild('message', $message->getMessage());
            $messageElement->addChild('createdAt', $message->getCreatedAt()->format('d.m.Y H:i:s'));

            if (count($message->getChild()) > 0) {
                $childsElement = $messageElement->addChild('childs');

                foreach ($message->getChild() as $childMessage) {
                    $childElement = $childsElement->addChild('message');
                    $childElement->addChild('guid', $childMessage->getGuid());
                    $childElement->addChild('message', $childMessage->getMessage());
                    $childElement->addChild('createdAt', $childMessage->getCreatedAt()->format('d.m.Y H:i:s'));
                }
            }
        }

        $this->xml->asXML(storage_path('messages.xml'));
    }

    /**
     * @return Collection
     * @throws Exception
     */
    public function getAll(): Collection
    {
        $collection = new Collection();

        foreach ($this->xml as $messageData) {
            $collection->push($this->loadMessage($messageData));
        }

        return $collection;
    }

    /**
     * @return Message|null
     * @throws Exception
     */
    public function getLast(): ?Message
    {
        return $this->getAll()->sort(function (Message $a, Message $b) {
            return $b->getCreatedAt() === $a->getCreatedAt() ? 0 : ($b->getCreatedAt() > $a->getCreatedAt() ? 1 : -1);
        })->first();
    }

    /**
     * @param string $guid
     * @return Message|null
     * @throws Exception
     */
    public function findByGuid(string $guid): ?Message
    {
        foreach ($this->xml as $messageData) {
            if ((string)$messageData->guid === $guid) {
                return $this->loadMessage($messageData);
            }
        }

        return null;
    }

    /**
     * @param SimpleXMLElement $element
     * @return Message
     * @throws Exception
     */
    protected function loadMessage(SimpleXMLElement $element): Message
    {
        $data = [];

        foreach ($this->mapping as $attributeName) {
            switch ($this->types[$attributeName]) {
                case 'string':
                    $data[$attributeName] = (string)$element->$attributeName;
                    break;
                case 'carbon':
                    $data[$attributeName] = new Carbon((string)$element->$attributeName);
                    break;
                case 'collection':
                    $data[$attributeName] = new Collection();
                    break;
                default:
                    throw new Exception('Unsupported field type');
            }
        }

        /** @var Message $message */
        $message = $this->hydrator->hydrate($data, Message::class);

        if (count($element->childs) > 0) {
            foreach ($element->childs as $childElement) {
                $message->addChild($this->loadMessage($childElement->message));
            }
        }

        return $message;
    }
}
