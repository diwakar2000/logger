<?php
/**
 * Class that handles logging.
 */

 class Logger {
    /**
     * Log file name.
     */
    public string $filename = 'log';

    /**
     * Log file name.
     */
    public string $ext = 'log';

    /**
     * Log path.
     */
    public string $path = '/';

    /**
     * Log path.
     */
    private bool $daywise = true;

    /**
     * Set log file path.
     */
    public function set_logfile_path( string $path ) : void {
        $this->filename = pathinfo( $path, PATHINFO_BASENAME );
        $this->ext      = pathinfo( $path, PATHINFO_EXTENSION );
        $this->path     = pathinfo( $path, PATHINFO_DIRNAME );
    }

    /**
     * Get log file path.
     */
    public function get_logfile_path() : string {
        try {
            if ( ! is_dir( $this->path ) ) {
                mkdir( $this->path, 0777, true );
            }

            if ( ! empty( $this->filename ) && ! file_exists( $this->get_logfile_path() ) ) {
                file_put_contents( $this->get_logfile_path(), '' );
            }
        } catch ( \Exception $e ) {
            throw new \DFileHandlerException( $e->getMessage() );
        }
        if ( ! $this->daywise ) {
            if ( ! empty( $this->filename ) ) {
                return $this->path . '/' . $this->filename;
            }
        } else {
            return $this->path . '/' . date('m') . '/' . date('d') . '.log';
        }
    }
 }