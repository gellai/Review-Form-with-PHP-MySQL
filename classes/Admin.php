<?php
/**
 *  Review Form with PHP & MySQL
 *  gellai.com
 * 
 *  January 2019
 * 
 *  Description:
 *  ------------
 *  Admin class to handle login and logout methods.
 *  Returning admin details from the user ID.
 * 
 */
class Admin extends Form
{
    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct();

        // Form login
        if(isset($_POST['formMethod']) && $_POST['formMethod'] == "login") {
            $this->_login();
        }

        // Form logout
        if(isset($_POST['formMethod']) && $_POST['formMethod'] == "logout") {
            $this->_logout();
        }
    }

    /**
     * Is admin logged in
     * @return boolean
     */
    public function isLoggedIn() {
        if( isset($_SESSION['admin']['id']) && isset($_SESSION['admin']['username']) ) {
            return true;
        }

        return false;
    }

    /**
     * Return the admin's username from its ID
     * @param type $id
     * @return type
     */
    public function getAdminNameById($id) {
        $sql = "
            SELECT username
            FROM   srf_admin
            WHERE  id = '$id';
        ";
        
        $query = mysqli_query($this->_db, $sql);
        $result = mysqli_fetch_assoc($query);

        return $result['username'] ? $result['username'] : "N/A";
    }

    /**
     * Admin login method
     */
    private function _login() {
        $username = isset($_POST['uname']) ? mysqli_real_escape_string($this->_db, trim($_POST['uname'])) : "";
        $password = isset($_POST['passwd']) ? mysqli_real_escape_string($this->_db, $_POST['passwd']) : "";

        $sql = "
            SELECT * 
            FROM   srf_admin
            WHERE  username = '$username';
        ";

        $query = mysqli_query($this->_db, $sql);
        $result = mysqli_fetch_assoc($query);

        // Password hash check
        if($result['password'] == md5($password)) {
            $_SESSION['admin'] = $result;
            $now = date("Y-m-d H:i:s");

            // Update last login date/time
            $sqlUpdate = "
                UPDATE srf_admin
                SET    last_login = '$now'
                WHERE  id = '".$result['id']."';
            ";

            $query = mysqli_query($this->_db, $sqlUpdate);
        }
        else {
            $this->_setError("loginError", "Invalid login!");
        }
    }

    /**
     * Logout method
     */
    private function _logout() {
        session_destroy();

        header("Location: admin");
        exit;
    }
}