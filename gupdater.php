<?php

#################
# User variables
#################
$googleDomainUser = "90a8sdf9d8sf0sa9";       # user that Google gives you when creating DDNS
$googleDomainPass = "7dsa6f7ds6afs8d7";       # password that Google gives you when creating DDNS
$googleDomain = "subdomain.domain.com";       # your domain/subdomain registered as DDNS
$gotIp = null;                                #
$savedIp = null;                              #
$logFile = "/srv/gupdater/error.log";         # file where errors will be saved
$ipFile = "/srv/gupdater/ip.save";            # file where ip will be saved locally
$repeatEach = 10;                             # minutes between tries


while( true ){

    # Get your own IP
    $gotIp = file_get_contents("https://ipecho.net/plain");

    # Check if we have created the initial file
    if( !file_exists($ipFile) ){
        file_put_contents($ipFile, "0.0.0.0");    
    }

    $savedIp = file_get_contents($ipFile);

    # Check if your IP is available
    if( empty($gotIp) || is_null($gotIp) ){
        file_put_contents($logFile, "[".time()."] Error obtaining your personal IP" . PHP_EOL, FILE_APPEND);
        goto restABit;
    }

    # If saved IP and obtained one are the same, exit.
    if( $savedIp === $gotIp ){
        file_put_contents($logFile, "[".time()."] Saved IP is still the same you had. Not updating" . PHP_EOL, FILE_APPEND);
        goto restABit;
    }

    # Save the new IP into the file
    file_put_contents($ipFile, $gotIp);

    # Change your personal domain to point your house in Google Domains
    $request = file_get_contents("https://".$googleDomainUser.":".$googleDomainPass."@domains.google.com/nic/update?hostname=".$googleDomain."&myip=".$gotIp);

    # Check if everything was ok
    if( (strpos($request, "good") === false) || (strpos($request, "nochg") === false) ){
        file_put_contents($logFile, "[".time()."] Error updating your IP: " . $request . PHP_EOL, FILE_APPEND);
        goto restABit;
    }

    # Wait some minutes to repeat again
    restABit:
    sleep($repeatEach * 60);
}
?>
