<?php

namespace Pnx\I9lJson;

/**
 * Write to a JSON file without requiring the contents to existing in memory.
 */
class IncrementalJsonFile implements \Serializable {

  /**
   * The temporary file resource being written to.
   *
   * @var resource
   */
  protected $file;

  /**
   * Flag for if a write has happened yet.
   *
   * @var bool
   */
  protected $firstWrite = TRUE;

  /**
   * IncrementalJsonFile constructor.
   *
   * @param string|null $json_prefix
   *   (Optional) The prefix to add to the file if required. An example might be
   *   assigning the blob of JSON to a variable or object, if the file should be
   *   treated as a script file.
   * @param string $temporaryFile
   *   (Optional) The temporary file that should store a buffer of the JSON.
   *   The file will not be deleted until cleaned up externally or by calling
   *   ::commit. This is a file instead of a directory because `tempnam` is not
   *   supported on custom stream wrappers.
   */
  public function __construct($json_prefix = NULL, $temporaryFile = NULL) {
    $this->file = $this->createFile($temporaryFile);

    if ($json_prefix) {
      $this->fwrite($json_prefix);
    }

    // All the incremental files will exist an array of incrementally written
    // data, so open an array initially, then close it in ::commit.
    $this->fwrite('[');
  }

  /**
   * Create a temporary file for writing to.
   *
   * @param null|string $temporaryFile
   *   The temporary file to use as a buffer.
   *
   * @return resource
   *   A file resource.
   */
  private function createFile($temporaryFile) {
    if ($temporaryFile === NULL) {
      // Explicitly create a temp file that is not cleaned up between threads.
      $directory = sys_get_temp_dir();
      $temporaryFile = tempnam($directory, 'incremental-json');
    }
    $file = fopen($temporaryFile, 'w+');
    if ($file === FALSE) {
      throw IncrementalJsonException::fileWriteError();
    }
    return $file;
  }

  /**
   * {@inheritdoc}
   */
  public function write($data) {
    if (!$this->firstWrite) {
      $this->fwrite(',');
    }
    $this->fwrite(json_encode($data));
    $this->firstWrite = FALSE;
  }

  /**
   * Write data to the temporary file.
   *
   * @param string $data
   *   Data to write.
   */
  private function fwrite($data) {
    if (!fwrite($this->file, $data)) {
      throw IncrementalJsonException::fileWriteError();
    }
  }

  /**
   * Get the URI of the temporary file.
   *
   * @return string
   *   The temporary file to get the URI of.
   */
  private function getFileUri() {
    return stream_get_meta_data($this->file)['uri'];
  }

  /**
   * Complete the JSON file and copy it to a destination.
   *
   * @param string $destination
   *   The destination to copy the completed incremental JSON file to.
   */
  public function commit($destination) {
    $temp_uri = $this->getFileUri();
    $this->fwrite(']');

    if (copy($this->getFileUri(), $destination) === FALSE) {
      throw IncrementalJsonException::commitCopyError();
    }

    fclose($this->file);
    unlink($temp_uri);
  }

  /**
   * {@inheritdoc}
   */
  public function serialize() {
    return serialize([$this->getFileUri(), $this->firstWrite]);
  }

  /**
   * {@inheritdoc}
   */
  public function unserialize($serialized) {
    list($file_uri, $firstWrite) = unserialize($serialized);
    $this->file = fopen($file_uri, 'a+');
    $this->firstWrite = $firstWrite;
  }

}
