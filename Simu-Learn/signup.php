<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = $password; // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: Categories.html");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
        body {
    --color-primary: #009579;
    --color-primary-dark: #007f67;
    --color-secondary: #252c6a;
    --color-error: #cc3333;
    --color-success: #4bb544;
    --border-radius: 4px;

    margin: 0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    background: url(student.jpg);    
    background-size: cover;
}

.container {
    width: 400px;
    max-width: 400px;
    margin: 1rem;
    padding: 2rem;
    box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
    border-radius: var(--border-radius);
    background: #ffffff;

}

.container,
.div__input,
.form__button {
    font: 500 1rem 'Quicksand', sans-serif;
}

.form--hidden {
    display: none;
}

.form > *:first-child {
    margin-top: 0;
}

.form > *:last-child {
    margin-bottom: 0;
}

.form__title {
    margin-bottom: 2rem;
    text-align: center;
}

.form__message {
    text-align: center;
    margin-bottom: 1rem;
}

.form__message--success {
    color: var(--color-success);
}

.form__message--error {
    color: var(--color-error);
}

.form__input-group {
    margin-bottom: 1rem;
}

.form__input {
    display: block;
    width: 100%;
    padding: 0.75rem;
    box-sizing: border-box;
    border-radius: var(--border-radius);
    border: 1px solid #dddddd;
    outline: none;
    background: #eeeeee;
    transition: background 0.2s, border-color 0.2s;
}

.form__input:focus {
    border-color: var(--color-primary);
    background: #ffffff;
}

.form__input--error {
    color: var(--color-error);
    border-color: var(--color-error);
}

.form__input-error-message {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: var(--color-error);
}

.form__button {
    width: 100%;
    padding: 1rem 2rem;
    font-weight: bold;
    font-size: 1.1rem;
    color: #ffffff;
    border: none;
    border-radius: var(--border-radius);
    outline: none;
    cursor: pointer;
    background: rgb(240, 216, 54);
}

.form__button:hover {
    background:rgb(212, 175, 8);
}

.form__button:active {
    transform: scale(0.98);
}

.form__text {
    text-align: center;
}

.form__link {
    color: var(--color-secondary);
    text-decoration: none;
    cursor: pointer;
    color: blue;
}

.form__link:hover {
    text-decoration: underline;
    color : red;   
}
        
    </style>
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="form__button" value="Submit">
                <br>
                <input type="reset" class="form__button" style ="background-color: grey;" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php" style="form__link">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>