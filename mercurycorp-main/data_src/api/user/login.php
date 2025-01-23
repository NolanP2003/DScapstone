 <!-- not in use but leaving it here for back up -->
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../../includes/db_config.php');
session_start();

// create a database connection
$mysqli = new mysqli($host, $dbUsername, $dbPassword, $database);
if ($mysqli -> connect_error){
    die("Connection failed: ".$mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // check if user exists in the users table
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt ->bind_param("s", $username);
    $stmt ->execute();
    $result = $stmt -> get_result();

    if ($result > 0){
        $user = $result->fetch_assoc();

        // verify the password
        if (password_verify($password, $user['password'])){
            // get the user role
            $role = $user['role'];

            //store user information in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $role;

            //redirect user based on role
            switch($role){
                case 'Physician':
                    header("Location: ../../../web_src/user_views/physician_dash.php");
                    break;
                case 'Resident':
                    header("Location: ../../../web_src/user_views/resident_dash.php");
                    break;
                case 'Nursing':
                    header("Location: ../../../web_src/user_views/nurse_dash.php");
                    break;
                case 'HR':
                    header("Location: ../../../web_src/user_views/hr_view/hr_dash.php");
                    break;
                default:
                    header("Location: ../../../web_src/user_views/employee_dash.php");
                    break;
            }
            exit;
        } else{
            echo "Invalid password. Please try again.";
        } 
        
        }else {
            echo "No account found with this username.";
        }
    $stmt ->close();

}
$mysqli->close();
?>