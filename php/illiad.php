<?php

/* Edit these variables */
$whitelist=array("primo.lclark.edu", "localhost"); /* whitelisted domains for requesting*/
$illiadDomain=""; /* e.g "https://illiad.lclark.edu"*/
$dsn = ""; // Data Source Name (DSN) from the file /usr/local/zend/etc/odbc.ini
$user = ""; // MSSQL database user
$password = ""; // MSSQL user password
$server="";  /* e.g. illiad.lclark.edu */
$database=""; /* e.g. ILLiadData*/


$referer=$_SERVER["HTTP_REFERER"];
$domain=parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

if (!in_array($domain, $whitelist)){exit();}

$data=array();
$username=$_GET["user"];

$dbhandle = mssql_connect($server, $user, $password)
or die("Couldn't connect to SQL Server on $myServer");

$selected = mssql_select_db($databse, $dbhandle)
or die("Couldn't open database $database");


/* Articles */

$query="select TransactionNumber, PhotoJournalTitle, PhotoJournalVolume, PhotoJournalIssue, PhotoJournalYear, PhotoArticleAuthor, PhotoArticleTitle, TransactionDate from Transactions where Username='$username' and TransactionStatus='Delivered to Web'";

//execute the SQL query and return records
$result = mssql_query($query);
$numRows = mssql_num_rows($result);
$data["referer"]=$domain;
$cat="Articles";
$c=1;
if ($numRows==0){$data[$cat]=false;}

else{


//display the results
  while($row = mssql_fetch_array($result))
  {
    $id=$row["TransactionNumber"];
    $jtitle=$row["PhotoJournalTitle"];
    $jvolume=$row["PhotoJournalVolume"];
    $jissue=$row["PhotoJournalIssue"];
    $year=$row["PhotoJournalYear"];
    $author=$row["PhotoArticleAuthor"];
    $title=$row["PhotoArticleTitle"];
    $url=$illiadDomain."/illiad/illiad.dll?Action=10&Form=75&Value=$id";
    $d=$row["TransactionDate"];
    $date=convertDate($d);

    $data[$cat][$c]["id"]=$id;
    $data[$cat][$c]["jtitle"]=$jtitle;
    $data[$cat][$c]["jvolume"]=$jvolume;
    $data[$cat][$c]["jissue"]=$jissue;
    $data[$cat][$c]["year"]=$year;
    $data[$cat][$c]["author"]=$author;
    $data[$cat][$c]["title"]=$title;
    $data[$cat][$c]["url"]=$url;
    $data[$cat][$c]["count"]=$c;
    $data[$cat][$c]["expires"]=$date;

    $c++;

  }
}

/* Requests */

$cat="Requests";

$sql="select TransactionNumber, RequestType,LoanAuthor,LoanTitle,PhotoJournalTitle, PhotoJournalVolume,PhotoJournalIssue,PhotoJournalYear,PhotoArticleAuthor,PhotoArticleTitle,DocumentType from Transactions where Username='$username' and TransactionStatus='Request Sent'";
//echo $sql;
$result = mssql_query($sql);
$numRows = mssql_num_rows($result);
if ($numRows==0){$data[$cat]=false;}

else{
  $d=1;
  while($row = mssql_fetch_array($result))
  {
    $requestType=$row["RequestType"];
    $loanAuthor=$row["LoanAuthor"];
    $loanTitle=$row["LoanTitle"];
    $articleTitle=$row["PhotoArticleTitle"];
    $articleAuthor=$row["PhotoArticleAuthor"];
    $documentType=$row["DocumentType"];

    $data[$cat][$d]["type"]=$requestType;
    $data[$cat][$d]["docType"]=$documentType;
    $data[$cat][$d]["count"]=$d;
    if ($requestType=="Article"){
      $data[$cat][$d]["author"]=$articleAuthor;
      $data[$cat][$d]["title"]=$articleTitle;


    }
    if ($requestType=="Loan"){
      $data[$cat][$d]["author"]=$loanAuthor;
      $data[$cat][$d]["title"]=$loanTitle;
    }
    $d++;
  }
}

echo json_encode($data);

function convertDate($orig){

  $pieces=explode(" ", $orig);
  array_pop($pieces);
  $date=implode(" ", $pieces);

  $datetime = new DateTime($date);
  $datetime->modify('+30 day');
  return $datetime->format('M jS, Y');


}




 ?>
