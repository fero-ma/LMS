<?php 
  class User {
    // DB stuff
    private $conn;

    //User Properties
    private $user_id;
    private $username;
    private $password;

    public $result;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    public function getDetails($user_id) {
      try
      {
          $query = "SELECT * FROM user_details u, classes c, departments d WHERE user_id = ?
                    AND ((u.class_id = c.class_id AND c.dept_id = d.dept_id) OR (c.in_charge = u.user_id AND c.dept_id = d.dept_id) OR (d.hod = u.user_id))
                   ";
          
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
    
    public function getAllocations($role) {
      try
      {
          if($role == 'hod' || $role == 'faculty' || $role == 'admin')
              $role='special';
          $query = "SELECT * FROM allocations WHERE role = ? or role = 'general'";
          
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

    public function getMaxAllocation($type, $role) {
      try
      {
          if($role == 'hod' || $role == 'faculty' || $role == 'admin')
              $role='special';
          $query = "SELECT * FROM allocations WHERE type = ? AND ( role = ? OR role = 'general' )";
          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$type, $role]);

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

    public function readSingle($email) {
      try
      {
          $this->email = $email;

          $query = 'SELECT * FROM users u, user_details d WHERE u.user_id = d.user_id AND email = ? AND active = 1';
          
          $stmt = $this->conn->prepare($query);
    
          $stmt->execute([$this->email]);
    
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