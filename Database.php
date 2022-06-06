
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
  public function select($table, $row = "*", $where = null, $order = null)
  {
    $query = 'SELECT ' . $row . ' FROM ' . $table;
    if ($where != null) {
      $query .= ' WHERE ' . $where;
    }
    if ($order != null) {
      $query .= ' ORDER BY ';
    }
    $Result = $this->DbCon->query($query);
    return $Result;
  }
  public function insert($table, $value, $row = null)
  {
    $insert = " INSERT INTO " . $table;
    if ($row != null) {
      $insert .= " (" . $row . " ) ";
    }
    for ($i = 0; $i < count($value); $i++) {
      if (is_string($value[$i])) {
        $value[$i] = '"' . $value[$i] . '"';
      }
    }
    $value = implode(',', $value);
    $insert .= ' VALUES (' . $value . ')';
    $ins = $this->DbCon->query($insert);
    if ($ins) {
      return true;
    } else {
      return false;
    }
  }

  public function delete($table, $where = null)
  {
    if ($where == null) {
      $delete = "DELETE " . $table;
    } else {
      $delete = "DELETE  FROM " . $table . " WHERE " . $where;
    }
    $del = $this->DbCon->query($delete);
    if ($del) {
      return true;
    } else {
      return false;
    }
  }
  public function update($table, $rows, $where)
  {
    $update = 'UPDATE ' . $table . ' SET ' . $rows . ' WHERE ' . $where;
    $query = $this->DbCon->query($update);
    if ($query) {
      return true;
    } else {
      return false;
    }
  }
}
