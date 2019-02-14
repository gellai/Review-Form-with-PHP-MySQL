<?php 
session_start();    

/**
 * Autoload classes
 * @param type $className
 * @return type
 */
function __autoload($className) {    
    return include_once('classes/' . $className . '.php');
}

// Instatntiet Admin class and
// check if user is logged in
$admin = new Admin;
if( !$admin->isLoggedIn() ) {
    header("Location: login");
    exit;
}

$reviews = $admin->loadAllReviews();    // Load all the reviews
$messages = $admin->_messages;          // Get general messages
$errors = $admin->_errors;              // Get error messages
?>

<!DOCTYPE html>
    <head>
        <title>Simple Review Form - Admin</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">    
        <link rel="stylesheet" href="css/bootstrap.3.4.0.min.css">
        <link rel="stylesheet" href="css/stylesheet.css?v=1.11">
        <script type="text/javascript" src="js/bootstrap.3.4.0.min.js"></script>
    </head>
    <body>
        <div class="container">
            <h1>Review Admin Page</h1>

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

            <? if(isset($reviews)): ?>
                <ul class="list-group">
                    <?php foreach($reviews as $review): ?>
                        <li class="list-group-item<?= $review['is_deleted'] ? " disabled" : "" ?>">
                            <p class="of-hidden">
                                <span class="float-l">
                                    <small>Review by: <?= $review['name'] ?></small>
                                </span>
                                <span class="float-r">
                                    <small class="text-muted"><?= substr($review['create_date'], 0, 16) ?></small>
                                </span>
                            </p>

                            <form>
                                <div class="star-rating small">
                                    <input type="radio" id="<?= $review['id'] ?>_rate5" name="formRating" disabled<?= $review['rating'] == '5' ? " checked" : "" ?> /><label for="<?= $review['id'] ?>_rate5"></label>
                                    <input type="radio" id="<?= $review['id'] ?>_rate4" name="formRating" disabled<?= $review['rating'] == '4' ? " checked" : "" ?> /><label for="<?= $review['id'] ?>_rate4"></label>
                                    <input type="radio" id="<?= $review['id'] ?>_rate3" name="formRating" disabled<?= $review['rating'] == '3' ? " checked" : "" ?> /><label for="<?= $review['id'] ?>_rate3"></label>
                                    <input type="radio" id="<?= $review['id'] ?>_rate2" name="formRating" disabled<?= $review['rating'] == '2' ? " checked" : "" ?> /><label for="<?= $review['id'] ?>_rate2"></label>
                                    <input type="radio" id="<?= $review['id'] ?>_rate1" name="formRating" disabled<?= $review['rating'] == '1' ? " checked" : "" ?> /><label for="<?= $review['id'] ?>_rate1"></label>
                                </div>
                            </form>

                            <h3 class="text-primary"><?= $review['title'] ?></h3>

                            <p><?= $review['review'] ?></p>

                            <?php if(!$review['is_deleted']): ?>
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <form method="post">
                                            <div class="col-sm-2">
                                                <?php 
                                                    $btnStatusColor = "btn-default";
                                                    if($review['status'] == "P") $btnStatusColor = "btn-warning";
                                                    if($review['status'] == "A") $btnStatusColor = "btn-success";
                                                    if($review['status'] == "C") $btnStatusColor = "btn-danger";
                                                ?>
                                                <select class="form-control <?= $btnStatusColor ?>" name="reviewStatus">
                                                    <option value="P"<?= $review['status'] == "P" ? " selected" : "" ?>>Pending</option>
                                                    <option value="A"<?= $review['status'] == "A" ? " selected" : "" ?>>Active</option>
                                                    <option value="C"<?= $review['status'] == "C" ? " selected" : "" ?>>Cancelled</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="reviewId" value="<?= $review['id'] ?>" />
                                            <input type="hidden" name="formMethod" value="update" />
                                            <input type="hidden" name="formKey" value="<?= md5("update=" . $review['id']) ?>" />
                                            <button type="submit" class="btn btn-default">Update</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="form-horizontal">
                                    <div class="form-group" style="margin-left:-7px;">
                                        <form method="post">
                                            <input type="hidden" name="reviewId" value="<?= $review['id'] ?>" />
                                            <input type="hidden" name="formMethod" value="delete" />
                                            <input type="hidden" name="formKey" value="<?= md5("delete=" . $review['id']) ?>" />
                                            <button type="submit" class="btn-link btn-xs"><span class="text-warning" onclick="return confirm('Do you want to delete?');">Delete Review</span></button>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="font-weight-bold text-danger txt-bold">DELETED</p>
                            <?php endif; ?>    
                            <p><small class="text-muted">Last updated: <?= $review['last_edit_date'] ?> by <?= $admin->getAdminNameById($review['admin_id']) ?></small></p>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if(!count($reviews)): ?>
                    <p class="alert alert-warning">There is no review!</p>
                <?php endif?>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="formMethod" value="logout" />
                <button type="submit" class="btn btn-default">Logout</button>
                <?php
                    $currentURL = $admin->getCurrentURL();
                    $pos = strpos($currentURL, "admin");
                    $homeURL = substr($currentURL, 0, $pos);
                ?>
                <a href="<?= $homeURL ?>" class="btn btn-default">Front Page</a>
                <small class="float-r text-muted">Logged in as <?= $_SESSION['admin']['username'] ?>. Last login <?= $_SESSION['admin']['last_login'] ?></small>
            </form>
        </div>
    </body>
</html>