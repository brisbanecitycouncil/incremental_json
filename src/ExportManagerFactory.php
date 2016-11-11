<?php

namespace Pnx\I9lJson;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Create instances of an export manager.
 */
class ExportManagerFactory implements ExportManagerFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public static function createInstance(NormalizerInterface $normalizer, $json_prefix = FALSE) {
    $serializer = new Serializer([$normalizer], [new JsonEncoder()]);
    $json_writer = new IncrementalJsonFile($json_prefix);
    return new ExportManager($serializer, $json_writer);
  }

}
