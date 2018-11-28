<?php

/*--------Base de données UCP--------*/
     $host = '10.40.128.23';
     $port = '5432';
     $database = 'db2018l3i_rdessain';
     $user = 'y2018l3i_rdessain';
     $password = 'A123456*';
     $connectString = 'host=' . $host . ' port=' . $port . ' dbname=' . $database .' user=' . $user . ' password=' . $password;


/*---------Base de données locale--------*/
     // $host = 'localhost';
     // $port = '5432';
     // $database = 'postgres';
     // $user = 'postgres';
     // $password = 'romain0499';
     // $connectString = 'host=' . $host . ' port=' . $port . ' dbname=' . $database .' user=' . $user . ' password=' . $password;


/*---------Connexion--------*/
     $link = pg_connect($connectString);
     if (!$link)
     die('Error: Could not connect: ' . pg_last_error());

?>
