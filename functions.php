<?php
/*
KETA GROUP PROJECT
30074191 / Keanu Farro
P467103  / Jack
Voushikha
SOURCE CODE: https://codeshack.io/crud-application-php-pdo-mysql/
*/
session_start();

///////////////////////////
/// Connect to Database ///
///////////////////////////
function pdo_connect_mysql() { // Performs connection to DB - however, it has all permissions... Perhaps i need to change this at some point??
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'painting_db';
    try {
        return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
	    exit('Failed to connect to database!');
    }
}

///////////////////////
/// File path check ///
///////////////////////
function get_path() {
    // Get the base directory path of the current script
    $base_directory = realpath(dirname(__FILE__));
    $base_folder_name = basename($base_directory);
    $current_directory = basename(dirname($_SERVER['SCRIPT_FILENAME']));

    // Initialize the path
    $path = '';
    if ($base_folder_name !== $current_directory){
        $path = "../";
    } 
    return $path;
}

///////////////////////
/// Search Function ///
///////////////////////
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['searchTarget'])) {
    $target = $_GET['searchTarget'];
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Check if search is not empty
    if (!empty($search)) {
        header("Location: " . $target . "?search=" . urlencode($search));
        exit();
    }
}

///////////////////
/// Login Check ///
///////////////////
function is_logged_in() { // If user is logged in, do not display login or register buttons, display weclome "FullName" and logout button instead.
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

function get_logged_in_user() {
    if (is_logged_in()) {
        return [
            // Collect user details
            'memid' => $_SESSION["memid"],
            'email' => $_SESSION["email"], 
            'fullname' => $_SESSION["name"],
            'role' => $_SESSION["role"],
        ];
    }
    return null;
}
    // If you need to check if the user is actually logged in please uncomment this and place it inside of the header.
    /*
    if ($user) {
        echo "Email: " . htmlspecialchars($user['email']) . "<br>";
        echo "Name: " . htmlspecialchars($user['fullname']) . "<br>";
        echo "Role: " . htmlspecialchars($user['role']) . "<br>";
    } else {
        echo "No user is logged in.";
    } */ 

/////////////////////////////
/// Authentication Checks ///
/////////////////////////////
function member_access($user){
    if (!$user) {
        return_to_index('Permission Denied - Not logged in.');
    }
}

function admin_access($user) {
    if (!$user) {
        return_to_index('Permission Denied - Not logged in.');
    }

    // Check if user has the correct role
    if (!isset($user['role']) || $user['role'] !== 2) {  // if not role type 2 then deny access - can try != 2 and it  might work better. 
        
        // Useful for tracking the data
        /*
        if ($user) {
            echo "Email: " . htmlspecialchars($user['email']) . "<br>";
            echo "Name: " . htmlspecialchars($user['fullname']) . "<br>";
            echo "Role: " . htmlspecialchars($user['role']) . "<br>";
            echo "Role Var Type: " . gettype($user['role']) . "<br>"; // try this in case
        } else {
            echo "No user is logged in.";
        }*/
         
        return_to_index('Permission Denied - Not admin.');
    }
}

function return_to_index($message) {
    $base_path = get_path();
    // Display the HTML with a meta refresh tag for redirection - displays for 5 seconds then sends back to
    echo "<!DOCTYPE html>
          <html lang='en'>
          <head>
              <meta charset='UTF-8'>
              <meta http-equiv='refresh' content='5;url={$base_path}index.php'>
              <title>Redirecting...</title>
          </head>
          <body>
              <p>" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</p>
              <p>You will be redirected in 5 seconds...</p>
          </body>
          </html>";
    exit();
}

///////////////////
/// HTML Header ///
///////////////////
function template_header($title) {
    $current_page = basename($_SERVER['SCRIPT_NAME']);
    $base_path = get_path();
    $user = get_logged_in_user();

    echo <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>$title</title>
        <link rel="stylesheet" type="text/css" href="{$base_path}styles.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
                integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="{$base_path}index.php">ACME Arts</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_path}index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_path}painting/paintings.php">Paintings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_path}artist/artists.php">Artists</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
EOT;

    // Check if the user has values
    if ($user) {
        // Display admin panel option first if user has admin role
        if (isset($user['role']) && $user['role'] == 2) {
            echo <<<EOT
            <li class="nav-item">
                <a class="nav-link" href="{$base_path}admin/admin_interface.php">Admin Panel</a>
            </li>
EOT;
        }
        // If the user is logged in then display their name and a logout option
        echo <<<EOT
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_path}member/member_settings.php">Account</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_path}member/member_logout.php">Logout</a>
                        </li>
EOT;
    } else { // other wise show the options to register and login
        echo <<<EOT
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_path}member/member_register.php">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{$base_path}member/member_login.php">Login</a>
                        </li>
EOT;
    }
    echo <<<EOT
                    </ul>
                    <form class="d-flex" action="" method="get">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search:" aria-label="Search">
                        <!-- ANDI keeps whining about the background image being decorative here -->
                        <select class="form-select me-2" name="searchTarget" aria-label="Search Target">
                            <option value="{$base_path}painting/paintings.php">Paintings</option>
                            <option value="{$base_path}artist/artists.php">Artists</option>
                        </select>
                        <button class="btn btn-secondary" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>
        <div class="content">
EOT;
}

function template_footer() {
    echo <<<EOT
        </div>
    </body>
</html>
EOT;
}