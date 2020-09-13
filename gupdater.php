
<?php

#################
# User variables
#################

$googleCredentials = [

    [
        'user'   => 'user-from-google',
        'pass'   => 'pass-from-google',
        'domain' => 'domain.tld'
    ],

    [
        'user'   => 'another-user-from-google',
        'pass'   => 'another-pass-from-google',
        'domain' => '*.domain.tld'
    ],

];


# file where errors will be saved
$logFile = __DIR__ . "/" . "error.log";         

# Get your public IP
$publicIp = file_get_contents("https://domains.google.com/checkip");

# Check if your public IP was taken successfully
if( empty($publicIp) || is_null($publicIp) ){
    file_put_contents( $logFile , "[".time()."] Error obtaining your public IP" . PHP_EOL, FILE_APPEND);
    die();
}

# Loop into configured domains to do the actions
foreach ( $googleCredentials as $credential ) {

    # Path where public ip will be cached locally
    //$ipCacheFile = __DIR__ . "/" . str_replace('*', '_', $credential['domain']) . ".cache";
    $ipCacheFile = __DIR__ . "/" . $credential['domain'] . ".cache";

    # Check if we have created (or create) the initial IP cache file
    if( !file_exists($ipCacheFile) ){
        file_put_contents($ipCacheFile, "0.0.0.0");
        echo $ipCacheFile;
    }

    # Get cached IP
    $cachedIp = file_get_contents( $ipCacheFile );

    # If cached and public IP are the same, exit.
    if( $cachedIp === $publicIp ){
        file_put_contents( $logFile , "[".time()."] Cached IP for ".$credential['domain']." is still the same you had. Not updating" . PHP_EOL, FILE_APPEND);
        continue;
    }

    # Save the new IP into the cache file
    file_put_contents( $ipCacheFile, $publicIp );

    # Change your personal domain to point your house in Google Domains
    $request = file_get_contents("https://".$credential['user'].":".$credential['pass']."@domains.google.com/nic/update?hostname=".$credential['domain']."&myip=".$publicIp);

    # Check if everything was ok
    if( (strpos($request, "good") === false) || (strpos($request, "nochg") === false) ){
        file_put_contents($logFile, "[".time()."] Error updating your IP for domain: " .$credential['domain']. ' - Response: ' . $request . PHP_EOL, FILE_APPEND);
        continue;
    }

}

         
