<?php

$config = [
    "private_key_bits" => 4096,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
];

$privateKey = openssl_pkey_new($config);

if (!$privateKey) {
    die("Erreur lors de la génération de la clé privée.\n");
}

openssl_pkey_export($privateKey, $privatePem);

$details = openssl_pkey_get_details($privateKey);
$publicPem = $details['key'];

if (!is_dir(__DIR__ . '/config/jwt')) {
    mkdir(__DIR__ . '/config/jwt', 0777, true);
}

file_put_contents(__DIR__ . '/config/jwt/private.pem', $privatePem);
file_put_contents(__DIR__ . '/config/jwt/public.pem', $publicPem);

echo "Clés générées dans config/jwt/\n";
