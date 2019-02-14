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
if($admin->isLoggedIn()) {
    header("location: admin");
    exit;
}

$errors = $admin->_errors;  // Get error messages
?>

<!DOCTYPE html>
    <head>
        <title>Simple Review Form - Admin</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">    
        <link rel="stylesheet" href="css/bootstrap.3.4.0.min.css">
        <link rel="stylesheet" href="css/stylesheet.css">
        <script type="text/javascript" src="js/bootstrap.3.4.0.min.js"></script>
    </head>
    <body>
        <div class="container">
            <form class="form-horizontal" method="post">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <h1>Admin Login</h1>
                        <small class="text-info">Username: admin | Password: demo1</small><br>
                        <small class="text-info">Username: moderator | Password: demo2</small>
                    </div>
                </div>

                <?php if(isset($errors)): ?>
                    <?php foreach($errors as $error): ?>
                        <p class="alert alert-danger"><?= $error ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>    

                <div class="form-group">
                    <label for="username" class="col-sm-2 control-label">Username:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="username" name="uname" placeholder="Username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-sm-2 control-label">Password:</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name="passwd" placeholder="Password">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="hidden" name="formMethod" value="login" />
                        <button type="submit" class="btn btn-primary">Login</button>
                        <?php
                            $currentURL = $admin->getCurrentURL();
                            $pos = strpos($currentURL, "login");
                            $homeURL = substr($currentURL, 0, $pos);
                        ?>
                        <a href="<?= $homeURL ?>" class="btn btn-default">Front Page</a>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>