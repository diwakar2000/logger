<?php
/**
 * PHP File handler.
 */

class FileHandler {

    /**
     * An instance of the filehandler object.
     */
    private static array $instance;

    private function __construct() {}

    public function init() : FileHandler {
        if ( empty( self::$instance['instance'] ) ) {
            self::$instance = new FileHandler();
        }
        return self::$instance['instance'];
    }

    /**
     * Return true if created, false on failure and null if already exists.
     */
    public function create_directory( string $dir ) : bool|null {
        if ( ! is_dir( $dir ) ) {
            return mkdir( $dir );
        }
    }

    /**
     * Insert data into a file.
     */
    public function put_content( string $file, string|array $content ) : int|false {
        if ( is_array( $content ) ) {
            $content = json_encode( $content );
        }
        return file_put_contents( $file, $content );
    }

    /**
     * Append content in a file.
     */
    public function append_content( string $file, string|array $content ) : int|false {
        if ( is_array( $content ) ) {
            $content = json_encode( $content );
        }
        try {
            $f = fopen( $file, 'a+' );
            if ( $f ) {
                $written = fwrite( $f, $content, strlen( $content ) );
                fclose( $f );
                return $written;
            }
        } catch ( \Exception $e ) {
            throw new \DFileHandlerException( $e->getMessage() );
        }
        return false;
    }

    /**
     * Append content in a file.
     */
    public function append_content_array( string $file, string|array $content ) : int|false {
        if ( ! is_array( $content ) ) {
            $content = [ 'content' => $content ];
        }
        try {
            $file_content = file_get_contents( $file );
            $file_content_array = json_decode( $file_content, true, 512, JSON_THROW_ON_ERROR );
            foreach ( $content as $index => $val ) {
                $file_content_array[ $index ] = $val;
            }
            return file_put_contents( $file, json_encode( $file_content_array ) );
        } catch( \JsonException $e ) {
            throw new \DFileJsonToArrayException( $e->getMessage() );
        } catch ( \Exception $e ) {
            throw new \DFileHandlerException( $e->getMessage() );
        }
        return false;
    }

    /**
     * Remove file.
     */
    public function delete( string $file ) : bool {
        if ( file_exists( $file ) ) {
            return unlink( $file );
        } else {
            throw new \DFileNotExistsException( "File: $file does not exists." );
        }
    }

    /**
     * Remove directory.
     */
    public function delete_dir( string $path, bool $resursive = false ) {
        if ( $resursive ) {
            if (is_dir($path)) {
                $files = scandir($path);
                foreach ($files as $file) {
                   if ($file !== '.' && $file !== '..') {
                      $filePath = $path . '/' . $file;
                      if (is_dir($filePath)) {
                         $this->delete_dir($filePath);
                      } else {
                         unlink($filePath);
                      }
                   }
                }
            }
        }
        rmdir($path);
    }
}
