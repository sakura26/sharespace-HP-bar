<?php
include 'config.php';
//healthbar lib
//150914 ethen heroeverything

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$conn->query(" SET NAMES UTF8;");

function getBar($barid){
	global $conn;
	$stmt = $conn->prepare("SELECT *, UNIX_TIMESTAMP() as c_timestamp FROM bar WHERE barid=?;"); //c_timestamp should not be used!
	$stmt->bind_param("s", $barid);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($myrow = $result->fetch_assoc()) {
		$ret = $myrow;
	}else {
	    $ret=null;
	}
  	return $ret;
}
function setBar($bardata){
	global $conn;
	$stmt = $conn->prepare("UPDATE bar SET owner=?, cost_avg=?, cost_interval=?, visitor_avg=?, value_current=?, value_max=?, rate=?, unit=?, last_update=NOW() WHERE barid=?;");
	$stmt->bind_param("sdidiidsi", $bardata["owner"], $bardata["cost_avg"], $bardata["cost_interval"], $bardata["visitor_avg"], $bardata["value_current"], $bardata["value_max"], $bardata["rate"], $bardata["unit"], $bardata["barid"]);
	$stmt->execute();
	if ($stmt->affected_rows==1)
		$res = true;
	else{
		error_log( '{ "status":"error", UPDATE bar SET owner='.$bardata["owner"].', cost_avg='.$bardata["cost_avg"].', cost_interval='.$bardata["cost_interval"].', visitor_avg='.$bardata["visitor_avg"].', value_current='.$bardata["value_current"].', value_max='. $bardata["value_max"].', rate='.$bardata["rate"].', unit='.$bardata["unit"].', last_update=NOW() WHERE barid='.$bardata["barid"].';"}'. PHP_EOL);
		$res = false;
	} // cost_avg/cost_interval

	return $res;
}
function addBar($bardata){
	global $conn;
	$stmt = $conn->prepare("INSERT INTO bar (barid, owner, cost_avg, cost_interval, visitor_avg, value_current, value_max, rate, unit, last_update)".
		 							" VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW());");
	$stmt->bind_param("isdidiids", $bardata["barid"], $bardata["owner"], $bardata["cost_avg"], $bardata["cost_interval"], $bardata["visitor_avg"], $bardata["value_current"], $bardata["value_max"], $bardata["rate"], $bardata["unit"]);
	$stmt->execute();
	if ($stmt->affected_rows==1)
		$res = true;
	else{
		$res = false; //TODO: log error
	}

	return $res;
}

function readHealth($bardata){
  return calcHealth($bardata);
}
function addHealth($barid, $value){
  $healthbarData = calcHealth(getBar($barid));
  $healthbarData["value_current"] += $value;
  /*
  check if state change
  alerts = findAlerts(healthbarData);
    if (alerts != null){
      addAlert(alerts);
      TTL = min(alerts);
    }
    else
      TTL = now+maxTTLdays;
  memcached.del(healthbarId);
  memcached.set(healthbarData);*/
  if (setBar($healthbarData))
	error_log( 'update bar success, '.$bardata["value_current"].'->'.$healthbarData["value_current"]. PHP_EOL);
  else
	error_log( 'failed to update bar?!'. PHP_EOL);
}
function setHealth($bardata, $value){
//  $healthbarData = getHealthData(healthbarId);
  $bardata["value_current"] = $value;
  /*
  check if state change
  alerts = findAlerts(healthbarData);
    if (alerts != null){
      addAlert(alerts);
      TTL = min(alerts);
    }
    else
      TTL = now+maxTTLdays;
  memcached.del(healthbarId);
  memcached.set(healthbarData);*/
  setBar($bardata);
}
function getHealthData($barid){/*
  healthbarData = memcached.get(healthbarId);
  if (healthbarData==null)
    healthbarData = mysql.get(healthbarId);*/
  return getBar($barid);
}
function calcHealth($bardata){
  $bardata["value_current"] = $bardata["value_current"] - 
                  ($bardata["cost_avg"]/$bardata["cost_interval"])*(time()-strtotime($bardata["last_update"])); //notice cron have its stopTimestamp!
  /*
  if (TTL==null){  //only on timeouted or init
    alerts = findAlerts(healthbarData);
    if (alerts != null){
      addAlert(alerts);
      TTL = min(alerts);
    }
    else
      TTL = now+maxTTLdays;
    memcached.set(healthbarData);
  }*/
  return $bardata;
  //notice: you can see there is no database/memcached update in normal view, because there is no need to do so
}
/*
function  findAlerts(healthbarData){
  if(SUM(cron[value])>0){ //cron is going up
    foreach alertDefine{
      if(vol_current<=alertDefine.value){
        alerts += new alert(healthbarId, alertDefine.id, now+(alertDefine.value-vol_current)/SUM(cron[value]));
      }
    }
  }
  if(SUM(cron[value])<0){ //cron is going down
    foreach alertDefine{
      if(vol_current>=alertDefine.value){
        alerts += new alert(healthbarId, alertDefine.id, now+(alertDefine.value-vol_current)/SUM(cron[value]));
      }
    }
  }
  return alerts;
}*/
?>
