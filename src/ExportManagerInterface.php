<?php

namespace Pnx\I9lJson;

/**
 * Provides an interface for the export manager.
 */
interface ExportManagerInterface {

  /**
   * Export data to a given filename.
   *
   * @param string $filename
   *   The filename to write to.
   * @param \Traversable $items
   *   Items to export.
   *
   * @return string
   *   Log output.
   */
  public function export($filename, \Traversable $items = []);

}
