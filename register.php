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

            .sidenav{
                background-color:#111;
                position: fixed;
                color: grey;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                padding-top: 10px;
                width: 25vh;
                height: 150%;
            }

            .sidenav a {
                padding: 20px 0px 20px 20px;
                text-decoration: none;
                cursor: pointer;
                color: grey;
                font-size: 18px;
            }

            .sidenav a.active{
                border-left-style: solid;
                border-left-color: rgb(255, 255, 255);
                border-left-width: 4px;
            }  
            .sidenav a:hover{
                border-left-style: solid;
                border-left-color: rgb(255, 255, 255);
                border-left-width: 4px;
            }

            @media only screen and (max-width: 650px){
                .sidenav a{
                    color: white;
                    font-size: 20px;
                    padding: 15px 20px 15px 1spx;
                }
                .sidenav a:hover{
                    border-left-style: solid;
                    border-left-color: rgb(255, 255, 255);
                    border-left-width: 5px;
                }
                .sidenav{
                    width: 3.5rem;
                }   
            } 


            .card {
                padding: 20px;
                min-width: 200px;
                max-width: 600px;
                width: 600px;
                /* border: outset; */
                box-shadow: 5px 10px #888888;
                position: absolute;
                top: 10%;
                left: 35%;
            }

            body {
                background: #EEEEEE;
                background-image: url('myLMS.png');
                background-size: cover;
                background-blend-mode: overlay;
            }

            .navbar-brand {
                display: block;
                left: 50%;
                position: absolute;
                text-align: center;
            }

            .col-11 .container-fluid {
                padding-top:50%;
            }

            .container-fluid {
                padding: 0px;
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-1">
                    <div class ="sidenav ">
                        <img class="rounded-circle ml-auto mx-auto" src="images(54).jpg" style = "height: 100px; width: 100px; object-fit: cover">
                        <h1 class="text-center">myLMS</h1>
                        <a href="index.php">Login</a>
                        <a class="active">Register</a>
                    </div>
                </div>
                <div class="col-11">
                    <div class="container-fluid">
                        <!-- <div class="row"> -->
                            <div class="card">
                                <h4 class = "text-center">REGISTER</h4>
                                <form method='POST' action = <?php $_SERVER['PHP_SELF'] ?>>
                                    <div class="form-group">
                                        Email:
                                        <input type="email" name="email" id="email" class="form-control"
                                        value="<?php echo isset($_POST['email'])? $_POST['email']:'' ?>"
                                        >
                                    </div>
                                    <div class="form-group">
                                        Full Name:
                                        <input type="text" name="fullname" id="fullname" class="form-control"
                                        value="<?php echo isset($_POST['fullname'])? $_POST['fullname']:'' ?>"
                                        >
                                    </div>
                                    <div class="form-group">
                                        Gender:
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="gender" id="gender" class="form-check-input"
                                                value="M">
                                                Male
                                            </label>
                                        </div>
                                        <div class="form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="gender" id="gender" class="form-check-input"
                                                value="F">
                                                Female
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        Role:   
                                        <select name="role" id="role" class="form-control">
                                            <option id='student'> Student </option>
                                            <option id='faculty'> Faculty </option>
                                            <option id='hod'> HOD </option>
                                        </select>
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
                                    <button type = "submit" class="btn btn-success btn-lg" name="submit">Register</button>
                                </form>
                            </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>