
<?php

# Create needed directories
mkdir(__DIR__ . "/error", 0755, true);
mkdir(__DIR__ . "/cache", 0755, true);
mkdir(__DIR__ . "/credentials", 0755, true);

# file where errors will be saved
$logFile = __DIR__ . "/error/" . "error.log";   

# Read credentials file
$credentialsFile = __DIR__ . "/credentials/credentials.json";

if( !file_exists($credentialsFile) ){
    file_put_contents( $logFile , "[".time()."] Credentials file not found" . PHP_EOL, FILE_APPEND);
    die();
}
$credentials = file_get_contents($credentialsFile);

# Parse and check credentials file
$credentials = json_decode($credentials);
if (json_last_error() !== JSON_ERROR_NONE) {
    file_put_contents( $logFile , "[".time()."] Credentials file is not a valid JSON" . PHP_EOL, FILE_APPEND);
    die();
}

# Check the format of each credential
foreach ($credentials as $index => $value ){
    # Check for the fields existance
    if( !array_key_exists ('user', $value ) || !preg_match('/[a-zA-Z0-9]/', $value['user']) ){
        file_put_contents( $logFile , "[".time()."] Check credentials format: user malformed" . PHP_EOL, FILE_APPEND);
        die();
    }

    if( !array_key_exists ('pass', $value ) || !preg_match('/[a-zA-Z0-9]/', $value['pass']) ){
        file_put_contents( $logFile , "[".time()."] Check credentials format: password malformed" . PHP_EOL, FILE_APPEND);
        die();
    }

    if( !array_key_exists ('domain', $value ) || !preg_match('/\A(\*.)*([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}\Z/', $value['domain']) ){
        file_put_contents( $logFile , "[".time()."] Check credentials format: domain malformed" . PHP_EOL, FILE_APPEND);
        die();
    }
}



/**
 * At this point we must have something like the
 * following, so we will use them
 * 
 * $credentials = [
 * [
 *      'user'   => 'user-from-google',
 *      'pass'   => 'pass-from-google',
 *      'domain' => 'domain.tld'
 * ],
 * [
 *      'user'   => 'another-user-from-google',
 *      'pass'   => 'another-user-from-google',
 *      'domain' => '*.domain.tld'
 * ],
 */

$googleCredentials = $credentials;



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
    $ipCacheFile = __DIR__ . "/cache/" . preg_replace('/[^A-Za-z0-9.]+/', '_', $credential['domain']);

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
