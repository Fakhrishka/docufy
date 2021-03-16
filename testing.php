<?php 	


// $sCreateDate = new DateTime('31/03/2021');
$sNowDate = new DateTime('NOW');

echo time() . "<br>";
// echo $sCreateDate->format();
// echo $sNowDate->format();
echo $sNowDate->getTimestamp();
$snew = new DateTime();
$snew->setTimestamp($sNowDate->getTimestamp());
// echo $snew->format('d-m-Y');
// $nDiffDays = $sCreateDate->diff($sNowDate);
// print_r($nDiffDays);
// start_date->setTimestamp(1372622987);
?>