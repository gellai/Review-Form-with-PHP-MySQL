<?php 
session_start();

/**
 * Auto load classes
 * @param type $className
 * @return type
 */
function __autoload($className) {    
    return include_once('classes/' . $className . '.php');
}

$form = new Form;
$reviews = $form->loadActiveRevies();
$formValues = $form->_formValues;
$messages = $form->_messages;
$errors = $form->_errors;

// Include captcha module
include_once('includes/GellaiCaptcha.php');

// Captcha settings
$param = array(
            'mode'   => "b64",      // Base64
            'length' => 5,          // Captcha length
            'type'   => "gif",      // Image type
            'tColor' => "646464",   // Text colour
            'bColor' => "F0F0F0",   // Background colour
            'lColor' => "949494"    // Line colour
        );
?>

<!DOCTYPE html>
    <head>
        <title>Simple Review Form</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">    
        <link rel="stylesheet" href="css/bootstrap.3.4.0.min.css">
        <link rel="stylesheet" href="css/stylesheet.css">
        <script type="text/javascript" src="js/bootstrap.3.4.0.min.js"></script>
    </head>
    <body>
        <div class="container">
            <?php if(isset($messages)): ?>
                <?php foreach($messages as $message): ?>
                    <p class="alert alert-success"><?= $message ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if(isset($errors)): ?>
                <?php foreach($errors as $error): ?>
                    <p class="alert alert-danger"><?= $error ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

            <h1>Customer Reviews</h1>
            <?php if(isset($reviews)): ?>
                <ul class="list-group">
                    <?php foreach($reviews as $review): ?>
                        <form><li class="list-group-item">
                            <p class="of-hidden">
                                <span class="float-l">
                                    <small>Review by: <?= $review['name'] ?></small>
                                </span>
                                <span class="float-r">
                                    <small class="text-muted"><?= substr($review['create_date'], 0, 16) ?></small>
                                </span>    
                            </p>
                            
                            <div class="star-rating small">
                                <input type="radio" id="<?= $review['id'] ?>_rate5" name="formRating" disabled<?= $review['rating'] == '5' ? " checked" : "" ?> />
                                <label for="<?= $review['id'] ?>_rate5"></label>
                                <input type="radio" id="<?= $review['id'] ?>_rate4" name="formRating" disabled<?= $review['rating'] == '4' ? " checked" : "" ?> />
                                <label for="<?= $review['id'] ?>_rate4"></label>
                                <input type="radio" id="<?= $review['id'] ?>_rate3" name="formRating" disabled<?= $review['rating'] == '3' ? " checked" : "" ?> />
                                <label for="<?= $review['id'] ?>_rate3"></label>
                                <input type="radio" id="<?= $review['id'] ?>_rate2" name="formRating" disabled<?= $review['rating'] == '2' ? " checked" : "" ?> />
                                <label for="<?= $review['id'] ?>_rate2"></label>
                                <input type="radio" id="<?= $review['id'] ?>_rate1" name="formRating" disabled<?= $review['rating'] == '1' ? " checked" : "" ?> />
                                <label for="<?= $review['id'] ?>_rate1"></label>
                            </div>

                            <h3 class="text-primary"><?= $review['title'] ?></h3>
                            <p><?= $review['review'] ?></p>    
                        </li></form>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h2>New Review</h2>
            <form method="POST">
                <div class="form-group<?= isset($errors['formNameError']) ? " has-error" : "" ?>">
                    <label for="reviewFormName">Display Name</label>
                    <input type="text" class="form-control" id="reviewFormName" name="formName" maxlength="50" placeholder="Name will be displayed with the review" value="<?= isset($formValues['formNameValue']) ? $formValues['formNameValue'] : "" ?>" />
                </div>

                <div class="form-group<?= isset($errors['formTitleError']) ? " has-error" : "" ?>">
                    <label for="reviewFormTitle">Title</label>
                    <input type="text" class="form-control" id="reviewFormTitle" name="formTitle" maxlength="50" placeholder="The title of the review" value="<?= isset($formValues['formTitleValue']) ? $formValues['formTitleValue'] : "" ?>" />
                </div>

                <div class="form-group<?= isset($errors['formEmailError']) ? " has-error" : "" ?>">
                    <label for="reviewFormEmail">E-mail Address</label>
                    <div class="input-group">
                        <span class="input-group-addon">@</span>
                        <input type="email" class="form-control" id="reviewFormEmail" name="formEmail" maxlength="50" placeholder="email@example.com" value="<?= isset($formValues['formEmailValue']) ? $formValues['formEmailValue'] : "" ?>" />
                    </div>    
                    <small id="helpBlock" class="help-block">The E-mail address will not be published.</small>
                </div>

                <div class="form-group">
                    <label for="reviewFormEmail">Rating</label>
                    <div class="star-rating">
                        <?php 
                            $formRatingValue = 0;
                            if(isset($formValues['formRatingValue']) && $formValues['formRatingValue'] == '1') { $formRatingValue = 1; }
                            if(isset($formValues['formRatingValue']) && $formValues['formRatingValue'] == '2') { $formRatingValue = 2; }
                            if(isset($formValues['formRatingValue']) && $formValues['formRatingValue'] == '3') { $formRatingValue = 3; }
                            if(isset($formValues['formRatingValue']) && $formValues['formRatingValue'] == '4') { $formRatingValue = 4; }
                            if(isset($formValues['formRatingValue']) && $formValues['formRatingValue'] == '5') { $formRatingValue = 5; }
                        ?>    
                        <input type="radio" id="rate5" name="formRating" value="5"<?= $formRatingValue == 5 ? " checked" : "" ?> />
                        <label for="rate5"></label>
                        <input type="radio" id="rate4" name="formRating" value="4"<?= $formRatingValue == 4 ? " checked" : "" ?> />
                        <label for="rate4"></label>
                        <input type="radio" id="rate3" name="formRating" value="3"<?= $formRatingValue == 3 ? " checked" : "" ?>/>
                        <label for="rate3"></label>
                        <input type="radio" id="rate2" name="formRating" value="2"<?= $formRatingValue == 2 ? " checked" : "" ?> />
                        <label for="rate2"></label>
                        <input type="radio" id="rate1" name="formRating" value="1"<?= $formRatingValue == 1 ? " checked" : "" ?> />
                        <label for="rate1"></label>
                    </div>
                </div>

                <div class="form-group<?= isset($errors['formReviewError']) ? " has-error" : "" ?>">
                    <label for="reviewFormReview">Review</label>
                    <textarea class="form-control" id="reviewFormReview" name="formReview" rows='10' cols='50' maxlength="2000" placeholder="Max 2000 characters"><?= isset($formValues['formReviewValue']) ? $formValues['formReviewValue'] : "" ?></textarea>
                </div>

                <div class="form-group <?= isset($errors['formCaptchaError']) ? " has-error" : "" ?>">
                    <label for="reviewFormCaptcha">
                        <?= $gCaptcha->getCaptcha($param); ?>
                    </label>    
                    <input type="text" class="form-control w-100p" id="reviewFormCaptcha" name="formCaptcha" maxlength="5" width="10" />
                    <input type="hidden" name="formMethod" value="save" />
                </div>
                <button type="submit" class="btn btn-primary">Post</button>
                <small class="float-r"><a href="<?= $form->getCurrentURL() ?>admin">Admin Login</a></small>
            </form>
        </div>
    </body>
</html>