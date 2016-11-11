<?php

namespace Pnx\I9lJson;

/**
 * Simple interface for writing a file chunks at a time.
 */
interface IncrementalFileInterface {

  /**
   * Write content to a file.
   *
   * @param string $content
   *   The content to write to the file.
   */
  public function write($content);

  /**
   * Commit the contents of the file to a place on disk.
   *
   * @param string $filename
   *   The place to write the completed file.
   */
  public function commit($filename);

}
