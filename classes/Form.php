<?php
/**
 *  Review Form with PHP & MySQL
 *  gellai.com
 *
 *  January 2019
 *
 *  Description:
 *  ------------
 *  Mainly handling form related tasks such as 
 *  form validation, error handling, reading/writing/updating database.
 * 
 */
class Form extends Db
{
    private $_formValues,
            $_messages,
            $_errors;

    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct();

        // Error during database connection
        if($this->_databaseError) {
            $this->_setError('databaseError', $this->_databaseError);
        }

        // Save
        if(isset($_POST['formMethod']) && $_POST['formMethod'] == "save") {
            $this->_validateFields()
                 ->_save();
        }

        // Update
        if(isset($_POST['formMethod']) && $_POST['formMethod'] == "update") {
            $this->_validateForm()
                 ->_update();
        }

        // Delete
        if(isset($_POST['formMethod']) && $_POST['formMethod'] == "delete") {
            $this->_validateForm()
                 ->_delete();
        }
    }

    /**
     * Accessing private properties
     * @param type $property
     * @return type
     */
    public function __get($property) {
        return $this->$property;
    }

    /**
     * Retrieve all the active records from the database
     * @return type
     */
    public function loadActiveRevies() {
        $sql = "
            SELECT *
            FROM srf_review
            WHERE status = 'A' AND is_deleted = '0'
            ORDER BY create_date DESC;
        ";

        $query = mysqli_query($this->_db, $sql);

        $results = array();
        while( $row = mysqli_fetch_assoc($query) ) {
            $results[] = $row;
        }

        if(mysqli_error($this->_db)) {
            $this->_setError('loadError', "There was an error during loading.");
        }

        return $results;
    }

    /**
     * Retrieve all records from the database
     * @return type
     */
    public function loadAllReviews() {
            $sql = "
            SELECT *
            FROM srf_review
            ORDER BY create_date DESC;    
        ";

        $query = mysqli_query($this->_db, $sql);

        $results = array();
        while( $row = mysqli_fetch_assoc($query) ) {
            $results[] = $row;
        }

        if(mysqli_error($this->_db)) {
            $this->_setError('loadError', "There was an error during loading.");
        }
        
        return $results;
    }

    /**
     * Return the current URL
     * @return string
     */
    public function getCurrentURL() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "" ? 'https' : 'http';
        $url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        return $url;
    }

    /**
     * Setting error messages into an array
     * @param type $name
     * @param type $value
     */
    protected function _setError($name, $value) {
        $this->_errors[$name] = $value;
    }    

    /**
     * Check if the form fields are valid
     * @return $this
     */
    private function _validateFields() {
        if( isset($_POST['formName']) && trim($_POST['formName']) ) {
            $this->_formValues['formNameValue'] = trim($_POST['formName']);
        }
        else {
            $this->_setError('formNameError', "Name is required!");
        }

        if( isset($_POST['formTitle']) && trim($_POST['formTitle']) ) {
            $this->_formValues['formTitleValue'] = trim($_POST['formTitle']);
        }
        else {
            $this->_setError('formTitleError', "Title is required!");
        }

        if( isset($_POST['formEmail']) && trim($_POST['formEmail']) ) {
            $this->_formValues['formEmailValue'] = trim($_POST['formEmail']);

            if( !filter_var($_POST['formEmail'], FILTER_VALIDATE_EMAIL) ) {
                $this->_setError('formEmailError', "Invalid E-mail Address!");
            }
        }
        else {
            $this->_setError('formEmailError', "E-mail Address is required!");
        }

        if( isset($_POST['formReview']) && trim($_POST['formReview']) ) {
            $this->_formValues['formReviewValue'] = trim($_POST['formReview']);

            if( strlen(trim($_POST['formReview'])) < 10 ) {
                $this->_setError('formReviewError', "The review must be minimum 10 characters long!");
            }
        }
        else {
            $this->_setError('formReviewError', "Please write some review!");
        }

        if( isset($_POST['formRating']) && ((int)$_POST['formRating'] >= 1 && (int)$_POST['formRating'] <= 5) ) {
            $this->_formValues['formRatingValue'] = $_POST['formRating'];
        }
        else {
            $this->_setError('formRatingError', "Please give a rating!");
        }

        if( !isset($_POST['formCaptcha']) || $_POST['formCaptcha'] != $_SESSION['gCaptcha'] ) {
            $this->_setError('formCaptchaError', "Invalid captcha!");
        }

        return $this;
    }

    /**
     * Check if the form key is valid to
     * prevent injections
     * @return $this
     */
    private function _validateForm() {
        $formMethod = $_POST['formMethod'];

        if( !isset($_POST['reviewId']) || !isset($_POST['formKey']) ) {
            $this->_setError('formError', "Invalid form!");
        }

        $formKey = md5($formMethod . "=" . $_POST['reviewId']);
        if($formKey != $_POST['formKey']) {
            $this->_setError('formError', "Invalid form!");
        }

        return $this;
    }

    /**
     * Save the form into MySQL database
     */
    private function _save() {
        if(!isset($this->_errors)) {
            $cleanName   = mysqli_real_escape_string($this->_db, $this->_formValues['formNameValue']);
            $cleanTitle  = mysqli_real_escape_string($this->_db, $this->_formValues['formTitleValue']);
            $cleanEmail  = mysqli_real_escape_string($this->_db, $this->_formValues['formEmailValue']);
            $cleanReview = mysqli_real_escape_string($this->_db, nl2br($this->_formValues['formReviewValue']));
            $cleanRating = mysqli_real_escape_string($this->_db, $this->_formValues['formRatingValue']);
            $now = date("Y-m-d H:i:s");

            $sql = "
                INSERT INTO srf_review (name, title, email, review, rating, status, last_edit_date, create_date)
                VALUES ('$cleanName', '$cleanTitle', '$cleanEmail', '$cleanReview', '$cleanRating', 'P', '$now', '$now');
            ";

            mysqli_query($this->_db, $sql);
            
            if(mysqli_affected_rows($this->_db) == 1) {
                $this->_messages['saveSuccess'] = "Your review has been added and will be checked for moderation.";

                $this->_formValues = array();
            }
            else {
                $this->_setError('saveError', "There has been an error, please try to save it again.");
            }
        }
    }

    /**
     * Update an existing record in the database
     */
    private function _update() {
        if(!isset($this->_errors)) {
            $cleanReviewId     = mysqli_real_escape_string($this->_db, $_POST['reviewId']);
            $cleanReviewStatus = mysqli_real_escape_string($this->_db, $_POST['reviewStatus']);
            $now = date("Y-m-d H:i:s");

            $sql = "
                UPDATE srf_review
                SET admin_id = '".$_SESSION['admin']['id']."',
                    status = '$cleanReviewStatus',
                    last_edit_date = '$now'
                WHERE id = '$cleanReviewId';
            ";

            mysqli_query($this->_db, $sql);

            if(mysqli_affected_rows($this->_db) == 1) {
                $this->_messages['saveSuccess'] = "The post has been updated.";
            }
            else {
                $this->_setError('saveError', "There has been an error, please try to save it again.");
            }
        }
    }

    /**
     * Delete a record - not deleted but marked as deleted.
     * It is still can be accessible from the admin page
     */
    private function _delete() {
        if(!isset($this->_errors)) {
            $cleanReviewId = mysqli_real_escape_string($this->_db, $_POST['reviewId']);
            $now = date("Y-m-d H:i:s");

            $sql = "
                UPDATE srf_review
                SET admin_id = '".$_SESSION['admin']['id']."',
                    is_deleted = '1',
                    last_edit_date = '$now'
                WHERE id =     '$cleanReviewId';
            ";

            mysqli_query($this->_db, $sql);

            if(mysqli_affected_rows($this->_db) == 1) {
                $this->_messages['saveSuccess'] = "The post has been deleted.";
            }
            else {
                $this->_setError('saveError', "There has been an error, please try to save it again.");
            }
        }
    }
}