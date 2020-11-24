<?php

namespace PouyaSoft\AppzaBundle\Services;

use DateTime;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use PouyaSoft\SDateBundle\Service\jDateService;

class SerializerListener implements SubscribingHandlerInterface
{
    private $jDateService;

    public function __construct(jDateService $jDateService)
    {
        $this->jDateService = $jDateService;
    }

    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, DateTime $date, array $type, Context $context)
    {
        return $this->jDateService->georgianToPersian($date, 'yyyy/MM/dd H:mm');
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'DateTime',
                'method' => 'serializeDateTimeToJson',
                'priority' => -10,
            ]
        ];
    }
}