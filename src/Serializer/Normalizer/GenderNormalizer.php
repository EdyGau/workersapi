<?php

namespace App\Serializer\Normalizer;

use App\Entity\Gender;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GenderNormalizer implements NormalizerInterface
{
    /**
     * @param $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Gender;
    }

    /**
     * @param $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     */
    public function normalize($object, string $format = null, array $context = []): float|array|bool|\ArrayObject|int|string|null
    {
        return $object->getName();
    }
}