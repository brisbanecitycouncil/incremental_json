<?php

namespace Pnx\I9lJson;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Provides an interface for an export manager factory.
 */
interface ExportManagerFactoryInterface {

  /**
   * Create an instance of the export factory.
   *
   * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface $normalizer
   *   Normalizer to use.
   * @param string $json_prefix
   *   String prefix for exported json contents.
   *
   * @return self
   *   New instance.
   */
  public static function createInstance(NormalizerInterface $normalizer, $json_prefix);

}
