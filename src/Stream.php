<?php

    namespace WOK\Stream;

    /**
     * The Stream class provide an interface
     * for resources manipulation
    **/
    class Stream {

        /**
         * @var     resource    $stream         Stream resource
        **/
        protected $stream;

        /**
         * @var     array       $meta           Stream meta data
        **/
        protected $meta = array();

        /**
         * Stream manipulation states
         * @var     bool        $seekable       Is stream seekable
         * @var     bool        $readable       Is stream readable
         * @var     bool        $writable       Is stream writable
        **/
        protected $seekable = false;
        protected $readable = false;
        protected $writable = false;


        /**
         * Instanciate Stream object
         * @param   resource        $resource         Stream
        **/
        public function __construct($resource) {

            if(!is_resource($resource))
                throw new \DomainException(__CLASS__.' must be defined with a resource as single parameter');

            $this->stream   = $resource;
            $this->meta     = stream_get_meta_data($this->stream);

            $mode = str_replace(['b','t'], '', $this->meta['mode']);
            $this->seekable = $this->meta['seekable'];
            $this->readable = in_array($mode, ['r', 'r+', 'w+', 'a+', 'x+', 'c+']);
            $this->writable = in_array($mode, ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+']);


        }


        /**
         * Is the stream readable
         * @return bool
        **/
        public function isReadable() {
            return $this->readable;
        }


        /**
         * Is the stream writable
         * @return bool
        **/
        public function isWritable() {
            return $this->writable;
        }

        /**
         * Is the stream seekable
         * @return bool
        **/
        public function isSeekable() {
            return $this->seekable;
        }


        /**
         * Get the stream size
         * @param   integer     $default        Default file size (should be null or zero)
         * @return  integer     File size
        **/
        public function getSize($default = null) {

            $stats = fstat($this->stream);
            return isset($stats['size']) ? $stats['size'] : $default;

        }



        /**
         * Get the stream meta data
         * @return  array       Returns the meta data list, value or null
        **/
        public function getMetaData() {

            return $this->meta;

        }


        /**
         * Get the stream meta data value
         * @param   string      $key        Meta key
         * @return  array|string|null       Returns the meta data list, value or null
        **/
        public function getMetaDataValue($key) {

            return (isset($this->meta[$key]) ? $this->meta[$key] : null);

        }


        /**
         * Get the stream resource itself
         * @return stream
        **/
        public function getResource() {
            return $this->stream;
        }


        /**
         * Get the stream content
         * @note The cursor will be reset to the start of the file if possible.
         * @return  string
        **/
        public function getContent() {

            if($this->isSeekable())
                $this->rewind();

            if (!$this->isReadable() || ($contents = stream_get_contents($this->stream)) === false) {
                throw new \RuntimeException('Could not get contents of not readable stream');
            }

            return $contents;

        }


        /**
         * Get the cursor position
         * @return  integer
        **/
        public function tell() {

            return ftell($this->stream);

        }


        /**
         * Move cursor position
         * @param   int     $offset         Cursor position
         * @param   int     $whence         Cursor movement mode
        **/
        public function seek($offset, $whence = SEEK_SET) {

            if(!$this->isSeekable())
                throw new \RuntimeException('Could not seek a not seekable stream');

            fseek($this->stream, $offset, $whence);

        }


        /**
         * Seek to the beginning of the stream
        **/
        public function rewind(){

            if (!$this->isSeekable() || rewind($this->stream) === false) {
                throw new \RuntimeException('Could not rewind stream');
            }

        }


        /**
         * Check if the cursor is at the end of the file
         * @return  bool    Returns wether the cursor is at the end of file or not
         *
         * @note    Because PHP internal `feof` functions only returns true when the file have been read,
         *          a more radical comparison between position and size is made.
        **/
        public function eof() {

            return ($this->tell() == $this->getSize());

        }


        /**
         * Read the stream from the current offset
         * @param integer       $length         Reading length bytes
         * @return              string          Returns the stream content
        **/
        public function read($length = null) {

            if(is_null($length))
                $length = $this->getSize(0);

            if (!$this->isReadable() || ($data = fread($this->stream, $length)) === false) {
                throw new \RuntimeException('Could not read from not readable stream');
            }

            return $data;

        }


        /**
         * Write within the stream
         * @param     string      $string         String to write
         * @return    string      Returns the written string
        **/
        public function write($string) {

            if (!$this->isWritable() || ($written = fwrite($this->stream, $string)) === false) {
                throw new \RuntimeException('Could not write in a not writable stream');
            }

            return $written;

        }


        /**
         * Close the stream
         * @return bool     Returns wether the stream has been closed or not
        **/
        public function close() {

            if (is_resource($this->stream)) {

                fclose($this->stream);

                $this->isReadable = false;
                $this->isWritable = false;
                $this->isSeekable = false;
                $this->meta       = array();

            }

        }


        /**
         * Get the stream content as a string
         * @return   string     Returns the stream content
        **/
        public function __toString() {

            try {

                return $this->getContent();

            }
            catch(\Exception $e) {

                return false;

            }

        }


        /**
         * Close the opened stream
        **/
        public function __destruct() {

            $this->close();

        }


    }
