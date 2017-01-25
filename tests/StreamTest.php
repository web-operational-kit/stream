<?php

    use PHPUnit\Framework\TestCase;

    use \WOK\Stream\Stream;

    class StreamTest extends TestCase {


        /**
         * Instanciate default header component
         * ---
        **/
        public function __construct() {

            $this->value    = 'value';
            $resource = fopen('php://temp', 'w+');;
            fwrite($resource, $this->value);

            $stats = fstat($resource);
            $this->size     = $stats['size'];
            $this->meta     = stream_get_meta_data($resource);
            $this->resource = $resource;

            $this->stream = new Stream($resource);

            $this->stream->rewind();

        }


        /**
         * Test retrieving stream resource
         * ---
        **/
        public function testGettingStream() {

            $this->assertEquals($this->resource, $this->stream->getResource(), 'Stream::getStream is supposed to return the resource');

        }


        /**
         * Test stream values retrieving
         * ---
        **/
        public function testGettingValuesMethods() {

            $this->assertEquals($this->value, $this->stream->getContent(), 'Stream::getContents() is supposed to return the stream value');
            $this->assertEquals($this->value, (string) $this->stream, 'Stream::__toString() is supposed to return the stream value');
            $this->assertEquals($this->size, $this->stream->getSize(), 'Stream::getSize() is supposed to return the stream size');

        }


        /**
         * Test stream manipulation
         * ---
        **/
        public function testManipulationMethods() {

            // Move cursor
            $this->stream->seek(2);
            $this->assertEquals(2, $this->stream->tell(), 'Stream::seek() is supposed to move the cursor position');
            $this->assertEquals(2, $this->stream->tell(), 'Stream::seek() : two checks is better than one');
            $this->assertEquals(substr($this->value, 2), $this->stream->read(), 'Stream::seek() : three checks is better than two :)');

            // Rewind
            $this->stream->seek(4);
            $this->stream->rewind();
            $this->assertEquals(0, $this->stream->tell(), 'Stream::rewind() is supposed to reset the cursor position');

            // Is at the end
            $this->stream->seek(0, SEEK_END);
            $this->assertTrue($this->stream->eof(), 'Stream::eof is supposed to return true at the end of stream');

        }


        /**
         * Test instanciate stream from a resource path
         * ---
        **/
        public function testCreateFromPath() {

            $stream = Stream::createFromPath($wrapper = 'php://memory', 'w+');
            $this->assertInstanceOf(Stream::class, $stream, 'Stream::createFromString() must return a stream object');

        }


        /**
         * Test instanciate stream with a string
         * ---
        **/
        public function testCreateWithString() {

            $stream = Stream::createWithString($stringValue = 'stringValue', $wrapper = 'php://memory');

            $this->assertInstanceOf(Stream::class, $stream, 'Stream::createFromString() must return a stream object');
            $this->assertEquals($stringValue, $stream->getContent(), 'Stream::createFromString() must define an initial value');


        }


    }
