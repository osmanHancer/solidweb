<?php
class dbSqliteC
{

  public $pdo;
  public $stm;
  public $dbname;
  public static dbSqliteC $current;
  public static  $currentDB;
  public function __construct()
  {
    try {
      $this->pdo = new PDO('sqlite:'.DB_PATH);
    } catch (PDOException $e) {
      var_dump($e);
    }
  }
  public static function getDB()
  {
    dbSqliteC::$current = new dbSqliteC();
    return dbSqliteC::$current;
  }

  public function bindParam($k, $v)
  {
    if (is_int($v))
      $this->stm->bindParam(':' . $k, $v, PDO::PARAM_INT);
    elseif (is_string($v))
      $this->stm->bindParam(':' . $k, $v, PDO::PARAM_STR);
    elseif (is_bool($v))
      $this->stm->bindParam(':' . $k, $v, PDO::PARAM_BOOL);
    else
      $this->stm->bindParam(':' . $k, $v);
  }

  public function exec($sql, $params)
  {


    $matches = null;
    preg_match_all("/(\:[A-z]\w+)(?![^\{]*\})(?![^\[]*\])/", $sql, $matches);
    //preg_match_all("/(\:[A-z]\w+)(?=([^'\\]*(\\.|'([^'\\]*\\.)*[^'\\]*'))*[^']*$)/", $sql, $matches);

    $this->stm = $this->pdo->prepare($sql);
    if (isset($matches) && is_array($matches[0])) {
      $pass = [];
      foreach ($matches[0] as $i => $k) {
        // echo "\n".$k."\n";
        $k = ltrim($k, ':');
        if (isset($params[$k])) {
          $pass[':' . $k] = $params[$k];
          $this->bindParam($k, $params[$k]);
        } else {
          $this->bindParam($k, '');
          $pass[$k] = '';
        }
      }
    }

    $r = $this->stm->execute();
    //$r = $this->stm->execute();

    return $r;
  }
  public function exec_old($sql, $params)
  {
    $this->stm = $this->pdo->prepare($sql);
    if (is_array($params)) {
      $pass = [];
      foreach ($params as $k => $v) {
        if (strpos($sql, ':' . $k) === false) {
          continue;
        }
        $pass[$k] = $v;
        $this->stm->bindParam(':' . $k, $v);
      }
    }
    // var_dump($pass);
    $r = $this->stm->execute($pass);
    return $r;
  }
  public function set($sql, $params = null)
  {
    try {
      $r = [];
      $this->exec($sql, $params);
      $r['ar'] = $this->stm->rowCount();
      $r['li'] = $this->pdo->lastInsertId();
    } catch (PDOException $e) {
      $r = $this->getError($e);
    }
    return $r;
  }
  public function all($sql, $params = null)
  {
    try {
      $this->exec($sql, $params);
      $r = $this->stm->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      // var_dump($e);
      $r = $this->getError($e);
    }
    return $r;
  }
  public function one($sql, $params = null)
  {
    try {

      $r = $this->exec($sql, $params);
      $r = $this->stm->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $r = $this->getError($e);
    }
    return $r;
  }
  public function getDict($sql, $pkey, $pval, $params = null)
  {
    $r = [];
    try {
      $this->exec($sql, $params);
      while ($row = $this->stm->fetch(PDO::FETCH_ASSOC))
        $r[$row[$pkey]] = $row[$pval];
    } catch (PDOException $e) {
      $r = $this->getError($e);
    }
    return $r;
  }
  public function getError($e)
  {
    return ['err' => $e->getCode(), 'msg' => $e->getMessage()];
  }
  public function getGroup($sql, $pkey, $pval, $params = null)
  {
    $r = [];
    try {
      $this->exec($sql, $params);
      while ($row = $this->stm->fetch(PDO::FETCH_ASSOC))
        $r[$row[$pkey]] = $row[$pval];
    } catch (PDOException $e) {
      $r = $this->getError($e);
    }
    return $r;
  }


  /** escape string */
  public static function escapeFields($value)
  {
    return preg_replace('/[^A-Za-z0-9_\{\}\[\]\:]/', '', $value);
  }


  /** return escaped params for pdo like this : "field1=:field1,field2=:field2"  */
  public static function getSetFields($q)
  {
    $fields = '';
    if (is_array($q))
      foreach ($q as $k => $v) {
        if ($k[0] == '_') continue;
        $k = dbSqliteC::escapeFields($k);
        $fields .= " $k= :$k,";
      }
    $fields = rtrim($fields, ',');
    return $fields;
  }


  public static function getInsertFields($q)
  {
    $fields = '';
    $values = '';
    if (is_array($q))
      foreach ($q as $k => $v) {
        if ($k[0] == '_') continue;
        $k = dbSqliteC::escapeFields($k);
        $fields .= "$k,";
        $values .= ":$k,";
      }
    $fields = rtrim($fields, ',');
    $values = rtrim($values, ',');
    return '(' . $fields . ') VALUES (' . $values . ')';
  }

}
