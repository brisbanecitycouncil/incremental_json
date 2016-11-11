<?php

namespace Pnx\I9lJson;

/**
 * Write a JSON file without having the entire contents in memory.
 */
class IncrementalJsonFile implements IncrementalFileInterface {

  /**
   * File resource.
   *
   * @var resource
   */
  protected $file;

  /**
   * If the first line has been written.
   *
   * @var bool
   */
  protected $firstWrite = TRUE;

  /**
   * Creates an instance of an IterativeJsonFile.
   */
  public function __construct($json_prefix = FALSE) {
    $this->file = tmpfile();
    if ($json_prefix) {
      fwrite($this->file, $json_prefix);
    }
  }

  /**
   * Get the URI to the temp JSON file.
   *
   * @return string
   *   The file path.
   */
  protected function getUri() {
    return stream_get_meta_data($this->file)['uri'];
  }

  /**
   * {@inheritdoc}
   */
  public function write($data) {
    fwrite($this->file, $this->firstWrite ? '[' : ',');
    fwrite($this->file, $data);
    $this->firstWrite = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function commit($location) {
    fwrite($this->file, ']');
    copy($this->getUri(), $location);
    unlink($this->getUri());
    fclose($this->file);
  }

}
