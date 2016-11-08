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
    const URL_API = 'http://otomoto.fixeads.com/api/open'; // sandbox, prod: https://ssl.otomoto.pl/api/open

    /**
     * dane do API client_id:client_secret
     *
     * @var string
     */
    const CLIENT_ID = 'key:secret';

    /**
     * nazwa uzytkownika w otomoto
     *
     * @var string
     */
    private $username = 'username';

    /**
     * haslo w otomoto
     *
     * @var string
     */
    private $password = 'account_password';

    /**
     * access_token
     *
     * @var string
     */
    private $access_token = '';

    /**
     * refresh_token
     *
     * @var string
     */
    private $refresh_token = '';

    /**
     * Konstruktor, jezeli stworzymy instancje z parametrami $username i $password to api polaczy sie ze wskazanym uzytkownikiem,
     * jak nie to przyjmie wartosci domyslne zawartne w $this->username i $this->password
     *
     * otomoto constructor.
     *
     * @param string $username nazwa uzytkownika
     * @param string $password haslo uzytkownika
     *
     */
    public function __construct($username = null, $password = null) {
        if (null != $username && null != $password) {
            $this->username = $username;
            $this->password = $password;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, otomoto::URL_API."/oauth/token");
        curl_setopt($ch,CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "grant_type=password&username=".$this->username."&password=".$this->password);
        curl_setopt($ch, CURLOPT_USERPWD, self::CLIENT_ID);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $head = curl_exec($ch);
        curl_close($ch);
        $json_decoded = json_decode($head, true);

        $this->access_token = $json_decoded['access_token'];
        $this->refresh_token = $json_decoded['refresh_token'];
    }

    /**
     * metoda do pobierania ogloszen
     *
     * @param int $limit ile ogloszen na stronie (0 - wszystkie ogloszenia)
     * @param int $page ktora strona
     * @return array
     */
    public function pobierz_ogloszenia($limit = 0, $page = 1) {
        $ch = curl_init();
        $url = (0 == $limit) ?
            self::URL_API."/account/adverts" :
            self::URL_API."/account/adverts?limit=$limit&page=$page";
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->access_token,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $head = curl_exec($ch);
        curl_close($ch);
        $json_decoded = json_decode($head, true);
        return $json_decoded;
    }

    public function wyswietl_ogloszenia($ogloszenia)
    {
        $i = 1;
        foreach ($ogloszenia['results'] as $ogloszenie) {
            echo "Ogloszenie nr: $i <br /> 
            ID: " . $ogloszenie['id'] . " <br />
            Status: " . $ogloszenie['status'] . " <br />
            Dodano: " . $ogloszenie['created_at'] . " <br />
            Ważne do: " . $ogloszenie['valid_to'] . " <br /><br />
            Tytuł: " . $ogloszenie['title'] . " <br />
            Opis: " . $ogloszenie['description'] . " <br /><br />
            Miasto: " . $ogloszenie['city']['pl'] . " <br /><br /><br />
            ";
            echo "Informacje szczegółowe:<br /><br />";
            foreach ($ogloszenie['params'] as $key => $value) {
                if ('features' == $key) {
                    foreach ($ogloszenie['params'][$key] as $feature) {
                        echo "$feature, ";
                    }
                    echo "<br />";
                    break;
                } else {
                    echo "$key: $value <br />";
                }
            }
            echo "<br /><br /> Cena:" . $ogloszenie['params']['price'][1]." ".$ogloszenie['params']['price']['currency'];
            echo "<br /><br />Zdjęcia:<br /><br />";
            foreach ($ogloszenie['photos'] as $zdjecie) {
                echo "<img src=\"" . $zdjecie['1080x720'] . "\"> <br />";
            }
            echo "--------------------------------------------------------------------------------------------------------------------------------------------- <br /><br />";
            $i++;
        }
    }
}
$otomoto = new otomoto();
$ogloszenia = $otomoto->pobierz_ogloszenia();
$otomoto->wyswietl_ogloszenia($ogloszenia);