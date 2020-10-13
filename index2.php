<?php
    if(session_status() == PHP_SESSION_NONE)
    {
        session_start();//start session if session not start
    }
    if(isset($_SESSION['user_id'])){
        header('location: dashboard.php');
    }

    // require('database/config.php');
    include_once('config/database.php');
    include_once('models/User.php');

    $msg='';            //The error msg we want to print
    $msgClass='';       //The type of error

    if(filter_has_var(INPUT_POST,'submit'))
    {
        $email = $_POST['email'];
        $pass = $_POST['pwd'];

        if(isset($email) && isset($pass))
        {   
            $database = new Database;
            $db = $database->connect();
            $user = new User($db);

            $result = $user->readSingle($email);

            $final = $result->fetch(PDO::FETCH_ASSOC);

            $num = $result->rowCount();

            if($num!=0 && filter_var($email,FILTER_VALIDATE_EMAIL))
            {   
                $user_id = $final['user_id'];
                $pwd = $final['pwd'];
                if(md5($pass)==$pwd)
                {   
                    session_start();
                    $_SESSION['user_id']=$user_id;
                    $_SESSION['role']=$final['role'];
                    $_SESSION['fullname']=$final['fullname'];
                    
                    switch($final['role']){
                        case 'student': header('Location: dashboard.php');break;
                        // case 'admin': header('Location: admin.html');break;
                        default: header('Location: faculty.php');break;
                    }
                }
                else
                {
                    $msg = "Incorrect Password";
                    $msgClass = "alert-danger";
                }
            }
            else
            {   $msg = "Invalid Email";
                $msgClass = "alert-danger";
            }
            $user->close();
        }
        else
        {
            $msg = "All fields have to be filled";
            $msgClass = "alert-danger";
        }
    }
?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- <link rel="stylesheet" href="assets/bootstrap.min.css">
        <script src = "assets/bootstrap.min.js"></script>
        <script src="assets/jquery.min.js"></script>
        <script src="assets/popper.min.js"></script> -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
                    <!--  -->
        <title>myLMS</title>

        <style>

            .card {
                padding: 20px;
            }

            section  {
                margin: 20px
            }

            body {
                padding-top: 80px;
            }

            .navbar-brand {
                display: block;
                left: 50%;
                position: absolute;
                text-align: center;
            }

            .card {
                min-width: 200px;
            }

        </style>

        <script>

        onLogin = function() {
            $('#register').hide();
            $('#login').show();
        }

        onRegister = function() {
            $('#login').hide();
            $('#register').show();
        }


        </script>

    </head>
    <body>
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top" style = "padding: 10px">
            <!-- <div class="navbar-header"> -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#cNS">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a href="" class="navbar-brand">
                    <img src="images(54).jpg" style = "height: 40px; width: 100px; object-fit: cover">
                </a>
            <!-- </div> -->
            <div class="collapse navbar-collapse" id="cNS">
                <ul class = "navbar-nav">
                    <li class="nav-item"><a href="#login" class = "nav-link">Home</a></li>
                    <li class="nav-item"><button class = "btn btn-link nav-link" onClick = "onLogin()">Login</button></li>
                    <li class="nav-item"><button class = "btn btn-link nav-link"onClick = "onRegister()">Register</button></li>
                </ul>
            </div>
        </nav>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-9 col-sm-9">
                    <hr>
                    <h1 class="text-center">Leave Management System</h1>
                    <span>This is a leave management system...</span>
                </div>
                <div class="col-xs-9 col-sm-3">
                    <section id="login">
                        <div class="card bg-light">
                                <h4 class = "text-center">LOGIN</h4>
                                <form method='POST' action = <?php $_SERVER['PHP_SELF'] ?>>
                                    <div class="form-group">
                                        Email:
                                        <input type="email" name="email" id="email" class="form-control"
                                        value="<?php echo isset($_POST['email'])? $_POST['email']:'' ?>"
                                        >
                                    </div>
                                    <div class="form-group">
                                        Password:
                                        <input type="password" name="pwd" id="pwd" class="form-control">
                                    </div>
                                    <div class = "alert <?php echo $msgClass ?>"><?php echo $msg ?></div>
                                    <button type = "submit" class="btn btn-primary" name="submit">Login</button>
                                </form>
                            </div>
                    </section>
                    <section id="register">
                        <div class="card bg-light">
                                <h4 class = "text-center">REGISTER</h4>
                                <form method='POST' action = <?php $_SERVER['PHP_SELF'] ?>>
                                    <div class="form-group">
                                        Email:
                                        <input type="email" name="email" id="email" class="form-control"
                                        value="<?php echo isset($_POST['email'])? $_POST['email']:'' ?>"
                                        >
                                    </div>
                                    <div class="form-group">
                                        Password:
                                        <input type="password" name="pwd" id="pwd" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        Confirm Password:
                                        <input type="password" name="cpwd" id="cpwd" class="form-control">
                                    </div>
                                    <div class = "alert <?php echo $msgClass ?>"><?php echo $msg ?></div>
                                    <button type = "submit" class="btn btn-primary" name="submit">Register</button>
                                </form>
                            </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>