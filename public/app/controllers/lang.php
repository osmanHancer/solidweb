<?php
require_once V_PATH . "/lang/_login.php";
class LangC extends Q_Controller
{
  function _init()
  {
    dbSqliteC::getDB();
  }
  public function langs()
  {

    $this->viewVars['lang_data'] = dbSqliteC::$current->all("SELECT * FROM langs");
    $this->viewVars['pageTitle'] = "Diller";
    $this->viewVars["TABLE_FIELDS"] = ["lid", "lang", "langTitle", "time", "timeCreated"];
    $this->viewVars["FIELDS_FORM"] = [
      "lid" => "INT",
      "lang" => "TEXT",
      "langTitle" => "TEXT",

    ];
  }
  public function keys()
  {
    $this->viewVars['keys'] = dbSqliteC::$current->all("SELECT * FROM lang_keys");
    $this->viewVars['pageTitle'] = "Keyler";
    $this->viewVars["TABLE_FIELDS"] = ["kid", "lkey", "ref", "time"];
    $this->viewVars["FIELDS_FORM"] = [
      "kid" => "INT",
      "lkey" => "TEXT",
      "ref" => "TEXT",

    ];
  }
  public function langdata()
  {
    $this->viewVars['lang'] = dbSqliteC::$current->one("
    SELECT * FROM langs 
    WHERE lid=:lid
", $_GET);


    $this->viewVars['data'] = dbSqliteC::$current->all("
    SELECT lk.lkey,lk.kid,ld.lval,ld.checked
    FROM lang_keys lk
    LEFT JOIN lang_data ld ON lk.kid=ld.kid AND ld.lid=:lid 
    ORDER BY lk.kid
", $_GET);
    $this->viewVars['key'] = $_GET["lid"];
    $this->viewVars['pageTitle'] = "Veriler";
    $this->viewVars["TABLE_FIELDS"] = ["kid", "lkey", "lval", "Rank", "Lang Check", "Edit"];
    $this->viewVars["FIELDS_FORM"] = [
      "lid" => "INT",
      "kid" => "INT",
      "lval" => "TEXT",
      "format" => "TEXT",
      "uid" => "INT",

    ];
  }

  public function svcLangSave()
  {
    $this->apiEnable();
    if ($_POST["action"] == "update") {
      $fields = dbSqliteC::getSetFields($_POST);
      $result = dbSqliteC::$current->set("UPDATE langs SET  $fields WHERE lid =:lid", $_POST);
      phpH::json($result);
    } else if ($_POST["action"] == "insert") {
      $fields = dbSqliteC::getInsertFields($_POST);
      $result = dbSqliteC::$current->set("INSERT INTO  langs  $fields", $_POST);
      phpH::json($result);
    } else if ($_POST["action"] == "delete") {

      $result = dbSqliteC::$current->set("DELETE FROM langs WHERE lid=:lid", $_POST);

      phpH::json($result);
    }
  }
  public function svcKeySave()
  {
    $this->apiEnable();
    $_POST["time"] = date('Y-m-d H:i:s');
    if ($_POST["_action"] == "update") {

      $fields = dbSqliteC::getSetFields($_POST);
      $sql = "UPDATE lang_keys SET  $fields WHERE kid=:kid";
    } else if ($_POST["_action"] == "insert") {
      var_dump($_POST);
      $_POST["timeCreated"] = date('Y-m-d H:i:s');
      $fields = dbSqliteC::getInsertFields($_POST);
      $sql = "INSERT INTO  lang_keys  $fields";
    } else if ($_POST["_action"] == "delete") {

      $sql = "DELETE FROM lang_keys WHERE kid=:kid ";
    }
    $result = dbSqliteC::$current->set($sql, $_POST);
    phpH::json($result);
  }
  public function svcDataSave()
  {

    $this->apiEnable();

    $val = dbSqliteC::$current->one("SELECT lid FROM lang_data WHERE  lid=:lid and kid=:kid ", $_POST);
    if ($val != null)
      $_POST["_action"] = "update";
    else
      $_POST["_action"] = "insert";
    if ($_POST["_action"] == "update") {
      $fields = dbSqliteC::getSetFields($_POST);
      $result = dbSqliteC::$current->set("UPDATE lang_data SET  $fields WHERE  lid=:lid and kid=:kid ", $_POST);
    } else if ($_POST["_action"] == "insert") {
      $fields = dbSqliteC::getInsertFields($_POST);
      $result = dbSqliteC::$current->set("INSERT INTO  lang_data  $fields ", $_POST);
    } else if ($_POST["_action"] == "delete") {
      $result = dbSqliteC::$current->set("DELETE FROM lang_data WHERE lid=:lid and kid=:kid", $_POST);
    }
    phpH::json($result);
  }
}
