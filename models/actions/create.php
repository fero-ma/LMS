<?php 
    if(session_status() == PHP_SESSION_NONE)
    {
        session_start();//start session if session not start
    }

    if(!isset($_SESSION['user_id'])){
        header('location: index.php');
    }

    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    include_once('../../config/database.php');
    include_once('../Application.php');
    include_once('../User.php');

    $database = new Database();
    $db = $database->connect();

    $user = new User($db);
    $application = new Application($db);

    $request = $_SERVER['REQUEST_METHOD'];
    header('Content-type: application/json');
    
    if($request=="POST")
    {
        if(    isset($_POST['reason']) && !empty($_POST['reason']) 
            && isset($_POST['from_date']) && !empty($_POST['from_date'])
            && isset($_POST['to_date']) && !empty($_POST['from_date'])
            )
        {
            $allocations = $user->getMaxAllocation($_POST['type'],$role);
            $allocations = $allocations->fetch(PDO::FETCH_ASSOC);

            $res = $application->getCount($_POST['type'], $user_id);
            $res= $res->fetch(PDO::FETCH_ASSOC);
            $count=$res['total'];

            $available = $allocations['days']-$count;

            $days=((strtotime($_POST['to_date'])-strtotime($_POST['from_date']))/86400)+1;

            if($days<=$available)
            {   $application->createNew($_POST, $days, $user_id);
                $result = [
                            'available' => "$available/$days",
                            'msg' => 'SUCCESS'
                        ];
            }
            else
                $result = [
                    'error' => 'EXCEEDED',
                    'available' => "$available/$days",
                    'msg' => 'You have no more leaves available!'
                ];

        }
        else if(isset($_POST['app_id']) && isset($_POST['status']) && isset($_POST['from_date']) )
        {
                if($_POST['status']=='WITHDRAWN')
                    if(date('Y-m-d') < $_POST['from_date'])
                    {
                        $application->withDraw($_POST['app_id']);    
                        $result = [
                            'msg' => 'SUCCESS'
                        ];
                    }
                    else
                        $result = [
                            'error' => 'NOT ALLOWED',
                            'msg' => 'You cannot withdraw now!'
                        ];
                else
                {
                    $application->changeStatus($_POST['app_id'], $user_id, $_POST['status']);
                    $result = [
                        'msg' => 'SUCCESS'
                    ];
                }
                
        }
        else
        {
            $result = [
                'error' => "MISSING_PARAMETERS",
                'msg' => "Please fill out all the fields!"
            ];
        }
        echo(json_encode($result));
    }
?>

<?php $user->close(); $application->close();?>