<?php

namespace Pnx\I9lJson\Tests;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Pnx\I9lJson\IncrementalJsonFile;


/**
 * @coversDefaultClass \Pnx\I9lJson\IncrementalJsonFile
 */
class IncrementalJsonFileTest extends TestCase {

  /**
   * A test file.
   *
   * @var \org\bovigo\vfs\vfsStreamFile
   */
  protected $testFile;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    vfsStream::setup();

    $this->testFile = vfsStream::newFile('root/foo');
  }

  /**
   * Test creating an incremental file.
   */
  public function testIncrementalFile() {
    $file = new IncrementalJsonFile('window.foo = ');
    $file->write('Foo');
    $file->write(['bar']);

    $file->commit($this->testFile->url());
    $this->assertEquals('window.foo = ["Foo",["bar"]]', file_get_contents($this->testFile->url()));
  }

  /**
   * Test serializing the file.
   */
  public function testSerialize() {
    $file = new IncrementalJsonFile('window.foo = ');
    $file->write('Foo');
    $file->write(['bar']);

    $file = unserialize(serialize($file));

    $file->write('baz');

    $file->commit($this->testFile->url());
    $this->assertEquals('window.foo = ["Foo",["bar"],"baz"]', file_get_contents($this->testFile->url()));
  }

  /**
   * Test serializing the file with no prior writes.
   */
  public function testSerializeNoWrites() {
    $file = new IncrementalJsonFile('window.foo = ');
    $file = unserialize(serialize($file));
    $file->write('foo');
    $file->write('bar');
    $file->commit($this->testFile->url());
    $this->assertEquals('window.foo = ["foo","bar"]', file_get_contents($this->testFile->url()));
  }

  /**
   * Test a custom location of a temp buffer file.
   */
  public function testCustomTempLocation() {
    $buffer = vfsStream::newFile('root/bar');
    $file = new IncrementalJsonFile('window.foo = ', $buffer->url());

    $file->write('foo');
    $this->assertEquals('window.foo = ["foo"', file_get_contents($buffer->url()));
  }

}
