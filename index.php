<?php

/**
 * Klasa obslugujaca API otomoto
 *
 */

class otomoto {

    /**
     * url do API otomoto
     *
     * @var string
     */
    const URL_API = 'http://otomoto.fixeads.com/api/open/oauth/token'; // sandbox

    /**
     * client id
     *
     * @var string
     */
    const CLIENT_ID = 'xxxx';

}

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, otomoto::URL_API);
curl_setopt($ch,CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
    "grant_type=password&username=otomoto&password=sandboxotomoto");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_USERPWD, "79:70f8c636a503d50ac6c411597b4cc402");


$head = curl_exec($ch);
//print_r(curl_error($ch));
curl_close($ch);

print_r($head);