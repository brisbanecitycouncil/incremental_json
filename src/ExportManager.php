<?php

namespace Pnx\I9lJson;

/**
 * Implementation of ExportManagerInterface
 */
class ExportManager implements ExportManagerInterface {

  /**
   * The serializer.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * A JSON file to write to.
   *
   * @var \Pnx\I9lJson\IncrementalJsonFile
   */
  protected $jsonFile;

  /**
   * Creates an instance of ExportManager.
   */
  public function __construct($serializer, $json_writer) {
    $this->serializer = $serializer;
    $this->jsonFile = $json_writer;
  }

  /**
   * {@inheritdoc}
   */
  public function export($filename, \Traversable $items) {
    $total_rows = count($items);
    $delta = 0;
    $buffer = '';
    foreach ($items as $item) {
      $delta++;
      $this->jsonFile->write($this->serializer->serialize($item, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]));
      $buffer .= "Completed $delta/$total_rows." . PHP_EOL;
    }
    $this->jsonFile->commit($filename);
    return $buffer;
  }

}
