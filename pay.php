<?php
require_once 'Rafinita.php';

$rafinita = new Rafinita();
$resp = $rafinita->sale($_POST);
$resolved = $rafinita->getResolvedResponse($resp);// returns ['error_message'] OR ['redirect_url'] OR ['redirect_form'] OR ['pending'] OR ['success']

if($resolved['error_message']){
    print $resolved['error_message'];
}
if($resolved['redirect_url']){
    print'<script>window.location.href = "'.$resolved['redirect_url'].'"</script>';
}
if($resolved['redirect_form']){
    print $resolved['redirect_form'];
}
if($resolved['pending']){
    print 'Ok. Lets wait a bit. We are processing your payment. Payment ID is: '.$resolved['pending'];
}
if($resolved['success']){
    print 'Success. Let`s celebrate... Payment ID is: '.$resolved['success']. ' ';
}
print '<br> <a href="'.$_SERVER['HTTP_ORIGIN'].'/logs/rafinita_request.log">You can see logs here</a>';