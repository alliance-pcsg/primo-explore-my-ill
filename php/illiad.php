<?php


//  $referer=$_SERVER["HTTP_REFERER"];
$domain=parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

/* the whitelist array only allows requests from the included domains  */
$whitelist=array("primo.lclark.edu", "localhost","alliance-primo-sb.hosted.exlibrisgroup.com");
if (!in_array($domain, $whitelist)){exit();}

/* ILLiad web platform key. For more info, check here: https://prometheus.atlas-sys.com/display/illiad/The+ILLiad+Web+Platform+API   */
/* key should look something like this: 71ed0900-97aa-4157-ad9f-e74bcfa5e709*/
$key="";    /* enter key here  */

$data=array();
$data["referer"]=$domain;
$username=strtolower($_GET["user"]);

$req="https://illiad.lclark.edu/ILLiadWebPlatform/Transaction/UserRequests/$username";

$ch = curl_init();
// set url
curl_setopt($ch, CURLOPT_URL, $req);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/xml","ApiKey: $key"));
//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// $output contains the output string
$output = curl_exec($ch);

// close curl resource to free up system resources
curl_close($ch);

$xml=simplexml_load_string($output);

$req_c=1;
$art_c=1;

foreach ($xml as $trans){

$status=$trans->TransactionStatus;
$id=$trans->TransactionNumber;

switch ($status){

case "Delivered to Web":

$cat="Articles";
$d=$trans->TransactionDate;
$date=convertDate($d);
$jtitle=$trans->PhotoJournalTitle;



$jvolume=$trans->PhotoJournalVolume;
$jissue=$trans->PhotoJournalIssue;
$year=$trans->PhotoJournalYear;
$author=$trans->PhotoArticleAuthor;
$title=$trans->PhotoArticleTitle[0];



$url="https://illiad.lclark.edu/illiad/illiad.dll?Action=10&Form=75&Value=$id";

$data[$cat][$art_c]["id"]="$id";
$data[$cat][$art_c]["jtitle"]="$jtitle";
$data[$cat][$art_c]["jvolume"]="$jvolume";
$data[$cat][$art_c]["jissue"]="$jissue";
$data[$cat][$art_c]["year"]="$year";
$data[$cat][$art_c]["author"]="$author";
$data[$cat][$art_c]["title"]="$title";
$data[$cat][$art_c]["url"]="$url";
$data[$cat][$art_c]["count"]="$art_c";
$data[$cat][$art_c]["expires"]="$date";

$art_c++;


break;
case "Request Sent":

$cat="Requests";

$requestType=$trans->RequestType;
$loanAuthor=$trans->LoanAuthor;
$loanTitle=$trans->LoanTitle;
$articleTitle=$trans->PhotoArticleTitle;
$articleAuthor=$trans->PhotoArticleAuthor;
$documentType=$trans->DocumentType;

$data[$cat][$req_c]["type"]="$requestType";
$data[$cat][$req_c]["docType"]="$documentType";
$data[$cat][$req_c]["count"]="$req_c";
if ($requestType=="Article"){
$data[$cat][$req_c]["author"]="$articleAuthor";
$data[$cat][$req_c]["title"]="$articleTitle";


}
if ($requestType=="Loan"){
$data[$cat][$req_c]["author"]="$loanAuthor";
$data[$cat][$req_c]["title"]="$loanTitle";


}
$req_c++;


//var_dump($transaction);


break;
default:
//do nothing

}







}



echo json_encode($data);

function convertDate($orig){

$pieces=explode("T", $orig);
array_pop($pieces);
$date=implode(" ", $pieces);

$datetime = new DateTime($date);
$datetime->modify('+30 day');
return $datetime->format('M jS, Y');


}



?>
