<?php

namespace Pnx\I9lJson;

/**
 * Exception thrown for errors with incremental JSON.
 */
class IncrementalJsonException extends \Exception {

  /**
   * @return static
   */
  public static function commitCopyError() {
    return new static('Could not copy completed file to the destination.');
  }

  /**
   * @return static
   */
  public static function fileWriteError() {
    return new static('Could not write content to the temporary JSON file.');
  }

  /**
   * @return static
   */
  public static function tempFileCreationErorr() {
    return new static('Could not create a temporary file to write to.');
  }

}
