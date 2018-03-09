<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 3/8/18
 * Time: 9:23 PM
 */

require_once __DIR__.'/../vendor/autoload.php';

use \Leeway\APIBank\Client as Client;

echo 'Creating client'.PHP_EOL;
$client = new Client();
echo 'Client created'.PHP_EOL;
echo PHP_EOL;
echo 'Logging in'.PHP_EOL;

try {
    $client->login(
        'mauro.chojrin@leewayweb.com',
        'WYGc1Jus0LLexuQ'
    );
    echo 'Login succesful'.PHP_EOL;
    $views = $client->getViews();
    foreach ( $views as $view ) {
        print_r( $view );
        echo 'Getting accounts for view '.$view['id'].PHP_EOL;
        $accounts = $client->getAccounts( $view['id'] );
        print_r($accounts);

    }
} catch ( Exception $e ) {
    echo $e->getMessage().PHP_EOL;
}
