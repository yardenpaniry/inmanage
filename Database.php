
<?php

class Database
{
  private $host = "127.0.0.1";
  private $username = "yarden";
  private $password = "test1234";
  private $database = "inmanage";
  public $DbCon;


  public function connect()
  {
    $con = new mysqli($this->host, $this->username, $this->password, $this->database);

    if ($con) {
      $this->DbCon = $con;

      return true;
    } else {
      return false;
    }
  }
  public function select($table, $row = "*", $where = null, $order = null){

  }
  public function insert($table, $value, $row = null)
  {

  }

  public function delete($table, $where = null)
  {
   
  }
  public function update($table, $rows, $where)
  {
   
  }

}