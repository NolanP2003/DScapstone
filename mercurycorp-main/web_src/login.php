
<?php
include("head.php");
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../data_src/includes/db_config.php');
session_start();
$error = "";
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

    if ($result ->num_rows > 0){
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
                    header("Location: user_views/physician_dash.php");
                    break;
                case 'Resident':
                    header("Location: user_views/resident_dash.php");
                    break;
                case 'Nursing':
                    header("Location: user_views/nurse_dash.php");
                    break;
                case 'HR':
                    header("Location: user_views/hr_view/hr_dash.php");
                    break;
                default:
                    header("Location: user_views/employee_dash.php");
                    break;
            }
            exit;
        } else{
          $error = "Invalid username or password.";
        } 
        
        }else {
          $error = "Invalid username or password.";
        }
    $stmt ->close();

}
$mysqli->close();
?>
<body>
    <?PHP
    include("header.php");
    include("navbar1.php");
    ?>
      <div id="intro" class="section-text col d-flex justify-content-center align-items-center" style="height: 900;">
        <div class="white-box text-center" style="background-color: white; padding: 70px; width: 500px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
          <div class="container mt-5">
            <h2 class="text-center">Login:</h2>

            <form action="" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label"><strong>Username</strong></label>
                <input type="text" class="form-control" id="username" name="username" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label"><strong>Password</strong></label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <br><br><br>
            <p>New user? 
              <a href="../data_src/api/user/create.php">
                  <button type="button">Create an Account</button>
              </a>
          </p>
          <!-- Error Message -->
          <?php if (!empty($error)): ?>
                    <div class="mt-3 text-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
          </div>
        </div>
      </div>
    
</body>

<?PHP
include("footer.php");
?>