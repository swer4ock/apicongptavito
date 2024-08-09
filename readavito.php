<?php

$url = 'https://www.avito.ru/moskva/predlozheniya_uslug/uslugi_fasovki_sypuchih_i_zhidkih_do_5kg_2793269614';

$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:93.0) Gecko/20100101 Firefox/93.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.5',
    'Accept-Encoding: gzip, deflate, br',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'TE: Trailers',
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close($ch);

echo $result;
