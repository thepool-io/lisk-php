<?php
/*
LISK-PHP
Made by karek314
https://github.com/karek314/lisk-php
The MIT License (MIT)

Copyright (c) 2017

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


function GetDelegateInfo($pk,$server){
	$url = $server.DELEGATE_ENDPOINT.'get?publicKey='.$pk;
	return MainFunction("GET",$url,false,false,true,4);
}


function GetVotersFor($pk,$server){
	$url = $server.VOTERS_ENDPOINT.$pk;
	return MainFunction("GET",$url,false,false,true,5);
}

function GetForgedByAccount($pk, $server,$start=false,$end=false)
{
    if ($start && $end) {
        $startTab = strptime($start, '%d-%m-%Y %k:%M:%S');
        $endTab = strptime($end, '%d-%m-%Y %k:%M:%S'); //01-12-2017 0:59:59
        $startTimestamp = mktime($startTab['tm_hour'], $startTab['tm_min'], $startTab['tm_sec'], $startTab['tm_mon']+1, $startTab['tm_mday'], $startTab['tm_year']+1900);
        $endTimestamp = mktime($endTab['tm_hour'], $endTab['tm_min'], $endTab['tm_sec'], $endTab['tm_mon']+1, $endTab['tm_mday'], $endTab['tm_year']+1900);
        var_dump($startTimestamp,$endTimestamp);
        $url = $server.DELEGATE_ENDPOINT.'forging/getForgedByAccount?generatorPublicKey='.$pk.'&start='.$startTimestamp.'&end='.$endTimestamp;
    } else {
        $url = $server.DELEGATE_ENDPOINT.'forging/getForgedByAccount?generatorPublicKey='.$pk;
    }
    return MainFunction("GET",$url,false,false,true,5);
}

function GetDelegatesList($server, $limit=100, $orderBy="rate", $offset=0){
    $url = $server.DELEGATE_ENDPOINT.'?limit='.$limit.'&offset='.$offset.'&orderBy='.$orderBy;
    return MainFunction("GET",$url,false,false,true,7);
}

function GetVotes($address,$server){
    $url = $server.ACCOUNTS.'delegates/?address='.$address;
    return MainFunction("GET",$url,false,false,true,5);
}

function GetBlocksBy($pk,$server,$offset=0,$orderBy='height',$orderType='desc'){
	$url = $server.BLOCKS_ENDPOINT.'?generatorPublicKey='.$pk.'&limit=100&offset='.$offset.'&orderBy='.$orderBy.':'.$orderType;
	return MainFunction("GET",$url,false,false,true,7);
}

function GetBlock($id,$server){
    $url = $server.BLOCKS_ENDPOINT.'get?id='.$id;
    return MainFunction("GET",$url,false,false,true,5);
}


function GetFees($server){
    $url = $server.BLOCKS_ENDPOINT.'/getFees';
    return MainFunction("GET",$url,false,false,true,5);
}

function GetSupply($server){
    $url = $server.BLOCKS_ENDPOINT.'/getSupply';
    return MainFunction("GET",$url,false,false,true,5);
}

function NetworkStatus($server){
    $url = $server.BLOCKS_ENDPOINT.'/getStatus';
    return MainFunction("GET",$url,false,false,true,5);
}

function AccountForAddress($address,$server){
	$url = $server.ACCOUNTS.'?address='.$address;
	return MainFunction("GET",$url,false,false,true,3);
}


function NodeStatus($server){
    $url = $server.NODE_STATUS;
	return MainFunction("GET",$url,false,false,true,3);
}


function SendTransaction($transaction_string,$server){
	$url = $server.SEND_TRANSACTION_ENDPOINT;
	return MainFunction("POST",$url,$transaction_string,true,true,6);
}


function MainFunction($method,$url,$body=false,$jsonBody=true,$jsonResponse=true,$timeout=3){
  $ch = curl_init($url);                                                                      
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);                                                                                      
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$timeout);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  $headers =  array();
  if ($body) {  
 	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);                                                             
	if ($jsonBody) {
		$headers = array('Content-Type: application/json','Content-Length: ' . strlen($body)); 
	}
  }
  $port = parse_url($url)['port'];
  if (!$port) {
  	if (parse_url($url)['scheme']=='https') {
		  $port="443";
  	} else {
  		$port="80";
  	}
  }
  array_push($headers, "minVersion: ".MINVERSION);
  array_push($headers, "os: ".OS);
  array_push($headers, "version: ".API_VERSION);
  array_push($headers, "port: ".$port);
  array_push($headers, "Accept-Language: en-GB");
  array_push($headers, "nethash: ".NETWORK_HASH);
  array_push($headers, "broadhash: ".NETWORK_HASH);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $result = curl_exec($ch);
  if ($jsonResponse) {
  	$result = json_decode($result, true); 
  }
  return $result;
}


?>