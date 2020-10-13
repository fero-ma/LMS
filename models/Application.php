<?php 
  class Application {
    // DB stuff
    private $conn;

    //User Properties
    private $from_date;
    private $to_date;
    private $requested_by;

    public $result;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    public function getCount($type, $user_id) {
      try
      {

          $query = "SELECT SUM(days) as total FROM applications WHERE type = '$type' AND requested_by = ? AND (status='PENDING' OR status='APPROVED') ";
          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$user_id]);

          return $stmt;
      }
      catch(Exception $exception)
      {
          http_response_code(504);
          echo json_encode(
          array('error' => $exception->getMessage())
          );
          die();
      } 
    }
    
    public function readAll($user_id) {
      try
      {
          $this->user_id = $user_id;

          $query = "SELECT * FROM applications a LEFT JOIN user_details u ON a.approved_by = u.user_id WHERE a.requested_by = ? ORDER BY app_id DESC";
          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$this->user_id]);

          return $stmt;
      }
      catch(Exception $exception)
      {
          http_response_code(504);
          echo json_encode(
          array('error' => $exception->getMessage())
          );
          die();
      } 
    }

    public function readPending($user_id, $role) {
      try
      {   if($role=='faculty')
              $query = "SELECT * FROM applications a, user_details u, classes c, departments d
                          WHERE a.requested_by = u.user_id
                          AND role = ? AND status = 'PENDING'
                          AND u.user_id = c.in_charge AND c.dept_id = d.dept_id
                          AND d.hod = $user_id
                          ORDER BY app_id DESC";
          elseif ($role == 'student')
              $query = "SELECT * FROM applications a, user_details u, classes c, departments d
                          WHERE a.requested_by = u.user_id
                          AND role = ? AND status = 'PENDING'
                          AND c.dept_id = d.dept_id
                          AND (u.class_id = c.class_id AND c.in_charge = $user_id)
                          ORDER BY app_id DESC";
          else
              $query = "SELECT * FROM applications a, user_details u, departments d
              WHERE a.requested_by = u.user_id
              AND d.hod = u.user_id
              AND role = ? AND status = 'PENDING'
              ORDER BY app_id DESC";

          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$role]);

          return $stmt;
      }
      catch(Exception $exception)
      {
          http_response_code(504);
          echo json_encode(
          array('error' => $exception->getMessage())
          );
          die();
      } 
    }

    public function readSingle($app_id) {
      try
      {
          $query = 'SELECT * FROM applications WHERE app_id = ?';
          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$app_id]);
    
          return $stmt;
      }
      catch(Exception $exception)
      {
          http_response_code(504);
          echo json_encode(
          array('error' => $exception->getMessage())
          );
          die();
      } 
    }

    public function createNew($arr,$days, $user_id) {
      try
      {
          $query = "INSERT INTO applications (from_date, to_date, days, reason, type, requested_by) values(?,?,$days,?,?,?)";
          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$arr['from_date'], $arr['to_date'], $arr['reason'], $arr['type'], $user_id]);
    
          return $stmt;
      }
      catch(Exception $exception)
      {
          http_response_code(504);
          echo json_encode(
          array('error' => $exception->getMessage())
          );
          die();
      }
    }

    public function changeStatus($app_id, $user_id, $decision) {
      try
      {
          $query = "UPDATE applications SET status = '$decision', approved_by = '$user_id' WHERE app_id = ?";
          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$app_id]);
    
          return $stmt;
      }
      catch(Exception $exception)
      {
          http_response_code(504);
          echo json_encode(
          array('error' => $exception->getMessage())
          );
          die();
      } 
    }


    public function withDraw($app_id) {
      try
      {
          $query = "UPDATE applications SET status = 'WITHDRAWN' WHERE app_id = ?";
          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$app_id]);
    
          return $stmt;
      }
      catch(Exception $exception)
      {
          http_response_code(504);
          echo json_encode(
          array('error' => $exception->getMessage())
          );
          die();
      } 
    }

    public function close()
    {
      $this->conn=null;
    }

  }