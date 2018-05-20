<?php
/*
Löscht alle IP-Adressen von Kommentaren die älter als 60 Tage sind!
Deletes the IP addresses of all commentators older than 60 days.
*/

define("C_DELETE_AFTER", "-60 days");

$cron = false;
if (php_sapi_name() == 'cli') {   
   if (!isset($_SERVER['TERM'])) {   
      $cron = true;   
   } 
}

if ($cron == false) {
   die('Diese Datei kann nicht über den Web-Browser aufgerufen werden! <br /> 
        This file cannot be called via the web browser!');    
}

/***************************************/
/* *** AB HIER NICHTS MEHR ÄNDERN! *** */
/* ** DON'T CHANGE ANYTHING FROM HERE **/
/***************************************/

include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php';

global $wpdb;

$rows = $wpdb->get_results("
    SELECT comment_ID, comment_post_ID, comment_author_IP, comment_date 
    FROM $wpdb->comments
    WHERE $wpdb->comments.comment_date < '" . date('Y-m-d H:i:s', strtotime(C_DELETE_AFTER)) . "'
    AND $wpdb->comments.comment_author_IP <> '' 
    ORDER BY $wpdb->comments.comment_date DESC
",ARRAY_A);


foreach( $rows as $row ) {
    $wpdb->update( 
        $wpdb->comments, 
        array( 
            'comment_author_IP' => '',
        ), 
        array( 'comment_ID' => $row['comment_ID'] ), 
        array( 
            '%s',
        ), 
        array( '%d' ) 
    );
}


?>