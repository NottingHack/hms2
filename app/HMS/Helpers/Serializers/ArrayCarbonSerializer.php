<?php

namespace HMS\Helpers\Serializers;

use Symfony\Component\Serializer\Serializer;
use Brainrepo\CarbonNormalizer\CarbonNormalizer;
use LaravelDoctrine\ORM\Serializers\ArrayEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ArrayCarbonSerializer
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serializer = new Serializer([new CarbonNormalizer, $this->getNormalizer()], [
            'array' => $this->getEncoder(),
        ]);
    }

    /**
     * @param $entity
     *
     * @return string
     */
    public function serialize($entity)
    {
        return $this->serializer->serialize($entity, 'array');
    }

    /**
     * @return GetSetMethodNormalizer
     */
    protected function getNormalizer()
    {
        return new GetSetMethodNormalizer;
    }

    /**
     * @return ArrayEncoder
     */
    protected function getEncoder()
    {
        return new ArrayEncoder;
    }
}
