<?php

use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';

// Create a Websocket server
$ws_worker = new Worker('websocket://0.0.0.0:8001');
$cons=[];
// Emitted when new connection come
$ws_worker->onConnect = function ($connection) use(&$cons) {
    $cons[]=$connection;
    echo $connection->id." Connected\n";
};

// Emitted when data received
$ws_worker->onMessage = function ($connection, $data) use(&$cons) {
  foreach($cons as $con){
    if($connection->id != $con->id){
    $con->send($data);
    }
  }
// $connection->send($data);
//     echo $connection->id." : ".$data."\n";
};

// Emitted when connection closed
$ws_worker->onClose = function ($connection) use(& $cons) {
  $cons = array_filter($cons, function($con) use ($connection) {
    if ($connection->id == $con->id) {
      echo $connection->id . " Disconnected\n";
      return false;
    }
    return true;
  });
    
};

// Run worker
Worker::runAll();