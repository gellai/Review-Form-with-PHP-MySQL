<?php
/**
 *  Review Form with PHP & MySQL
 *  gellai.com
 *
 *  January 2019
 *
 *  Description:
 *  ------------
 *  Abstract class to connect to the MySQL Database, this class cannot be instantiated.
 *  Credentials and connection details are REQUIRED.
 *
 *  HOST    - The domain/IP address of the database server, usually 'localhost'
 *  USER    - Username to connect to the database
 *  PASS    - Password for connection
 *  DB      - Name of the database
 *
 */
class Db
{
    const HOST = "localhost";   // Domain/IP address
    const USER = "";            // Username
    const PASS = "";            // Password
    const DB   = "";            // Database name

    protected $_db,
              $_databaseError;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->_connectDB();
    }

    /**
     * Creating database connection
     */
    private function _connectDB() {
        $this->_db = @mysqli_connect(self::HOST, self::USER, self::PASS, self::DB);
        
        if(mysqli_connect_errno()) {
            $this->_databaseError = "Database connection error " . mysqli_connect_errno() . ": " . mysqli_connect_error();
        }
    }

    /**
     * Cleaning up the special characters for the SQL statement
     * @param type $string
     * @return type
     */
    protected function _cleanString($string) {
        $cleanString = trim(mysqli_real_escape_string($this->_db, $string));
        
        return $cleanString;
    }
}
