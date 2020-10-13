<?php
    if(session_status() == PHP_SESSION_NONE)
    {
        session_start();//start session if session not start
    }

    if(!isset($_SESSION['user_id'])){
        header('location: index.php');
    } else if($_SESSION['role']=='student')
        header('location: dashboard.php');

    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $fullname = $_SESSION['fullname'];

    include_once('config/database.php');
    include_once('models/Application.php');
    include_once('models/User.php');

    $database = new Database();
    $db = $database->connect();

    $user = new User($db);
    $application = new Application($db);

    $result = $application->readAll($user_id);

    $allocations = $user->getAllocations($role);
    
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- <link rel="stylesheet" href="assets/bootstrap.min.css">
        <script src = "assets/bootstrap.min.js"></script>
        <script src="assets/jquery.min.js"></script>
        <script src="assets/popper.min.js"></script> -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
                    <!--  -->
        <link rel="stylesheet" href="assets/mycss.min.css">
        
        <title>myLMS</title>

        <script>
                onSubmit = function () {
                    var from_date = $('#from_date').val();
                    var to_date = $('#to_date').val();
                    var type = $('#type').val();
                    var reason = $('#reason').val();
                    $.ajax({
                        url: 'models/actions/create.php',
                        type: 'post',
                        data: {
                            'from_date': from_date,
                            'to_date': to_date,
                            'type': type,
                            'reason': reason
                        },
                        success: function (result) {
                            if(result.error) {
                                $('#d_head').html(result.error);
                                $('#d_body').html(result.msg);
                            } else {
                                $('#d_head').html('Success!');
                                $('#d_body').html("Your leave application has been submitted! Please wait for your superior's response!");
                            }

                            $('#createNewApplication').modal('hide');
                            $('#dialog').modal('show');
                        },
                        error: function (msg) {
                            $('#createNewApplication').modal('hide');
                            $('#d_head').html('ERROR');
                            $('#d_head').html('An internal error has occurred while processing your request!');
                            $('#dailog').modal('show');
                        }
                    });
                };

                withDraw = function (id) {
                    $.ajax({
                        url: 'models/actions/create.php',
                        type: 'post',
                        data: {
                            'app_id': id,
                            'status': 'WITHDRAWN'
                        },
                        success: function (result) {
                            if(result.error) {
                                $('#d_head').html(result.error);
                                $('#d_body').html(result.msg);
                            } else {
                                $('#d_head').html('Success!');
                                $('#d_body').html("Your leave application has been withdrawn! :(");
                            }

                            $('#createNewApplication').modal('hide');
                            $('#dialog').modal('show');
                        },
                        error: function (msg) {
                            $('#createNewApplication').modal('hide');
                            $('#d_head').html('ERROR');
                            $('#d_head').html('An internal error has occurred while processing your request!');
                            $('#dailog').modal('show');
                        }
                    });
                };
                
        </script>

    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-2">
                    <div class ="sidenav ">
                        <img class="rounded-circle ml-auto mx-auto" src="images(54).jpg" style = "height: 100px; width: 100px; object-fit: cover">
                        <a href="about.php"><strong><?php echo $fullname ?></strong></a>
                        <a href="faculty.php">Dashboard</a>
                        <a class="active">My Applications</a>
                        <a href="models/actions/logout.php">Logout <i class="fa fa-sign-out"></i></a>
                    </div>
                </div>
                <div class="col-10">
                    <div class="container">
                        <hr>
                        <div class="row">
                            <div class="col">
                                <h4 style = "display: inline">Your applications:</h4>
                            </div>
                            <div class="col">
                                <button type = "button" class = "btn btn-primary float-right" data-toggle="modal" data-target="#createNewApplication">New Application</button>
                            </div>
                            <div class="modal fade" id="createNewApplication">
                                <div class="modal-dialog">
                                <div class="modal-content">               
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h4 class="modal-title">New Leave Application</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <!-- Modal body -->
                                        <div class="modal-body">
                                            <form>
                                                <div class="form-group">
                                                    <div class="row">
                                                            <?php $date = date("Y-m-d"); ?>
                                                            <div class="col-sm-6">
                                                                    From:
                                                                    <input type="date" required="required" name="from_date" id="from_date" class="form-control" onChange = "getDuration()" value = "<?php echo $date; ?>" min="<?php echo $date; ?>">
                                                            </div>
                                                            <div class="col-sm-6">
                                                                    To:
                                                                    <input type="date" required="required" name="to_date" id="to_date" class="form-control" onChange = "getDuration()" value = "<?php echo $date; ?>" min="<?php echo $date; ?>">
                                                            </div>
                                                        </div>                                       
                                                    </div>
                                                <div class="form-group">
                                                    No of days:
                                                    <input type="text" name="days" id="days" class="form-control" value="1 days" disabled>
                                                </div>
                                                <div class="form-group">
                                                    Type of Leave:
                                                    <select name="type" id="type" class="form-control">
                                                        <option id="PERSONAL">PERSONAL</option>
                                                        <option id="HEALTH">HEALTH</option>
                                                        <option id="ON-DUTY">ON-DUTY</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    Reason for leave:
                                                    <textarea class = "form-control" name="reason" id="reason" cols="30" rows="3"></textarea>
                                                </div>

                                                <button type="button" class="btn btn-success" id="submit" onClick = "onSubmit()">Submit</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>  
                            </div>
                            <div class="modal fade" id="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 style = "display: inline" id="d_head"></h4>
                                            <button class="btn btn-danger" data-dismiss = "modal" onClick = "location.reload()"><i class="fa fa-refresh"></i></button>
                                        </div>
                                        <div class="modal-body">
                                            <p id="d_body"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>FROM</th>
                                        <th>TO</th>
                                        <th>DAYS</th>
                                        <th>TYPE</th>
                                        <th>REASON</th>
                                        <th>STATUS</th>
                                        <th>APPROVED BY</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $result->fetch(PDO::FETCH_ASSOC)) { extract($row);?>
                                    <tr>
                                        <td><?php echo $app_id ?></td>
                                        <td><?php echo $from_date ?></td>
                                        <td><?php echo $to_date ?></td>
                                        <td><?php echo $days.' days' ?></td>
                                        <td><?php echo $type ?></td>
                                        <td class="font-italic"><?php echo $reason ?></td>
                                        <td>
                                            <span style = "font-size: 100%;" class = "badge <?php echo $status == 'PENDING' ? 'badge-secondary':($status=='APPROVED'? 'badge-success':'badge-danger' );?>">
                                                <?php echo $status ?>
                                            </span>
                                        </td>
                                        <td><?php echo isset($approved_by)? $fullname:'-nil-' ?></td>    
                                        <td>
                                            <button type="button" class="btn btn-danger"
                                                    onClick = "withDraw(<?php echo $app_id ?>)"
                                                    <?php echo $status == 'WITHDRAWN' || (date('Y-m-d') >= $from_date) ? 'disabled':'' ?>>
                                                WITHDRAW
                                            </button>
                                        </td>
                                        </form>            
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </body>
</html>

<?php $user->close(); $application->close();?>