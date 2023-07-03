<?php
//provides access to WP environment
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');  
 
  /* import
  you get the following information for each file:
  $_FILES['field_name']['name']
  $_FILES['field_name']['size']
  $_FILES['field_name']['type']
  $_FILES['field_name']['tmp_name']
 */
  
if($_FILES['upload']['name']) {
  if(!$_FILES['upload']['error']) {
    //validate the file
    $new_file_name = strtolower($_FILES['upload']['tmp_name']);
    //can't be larger than 300 KB 
    if($_FILES['upload']['size'] > (30000000)) {
      //wp_die generates a visually appealing message element
      wp_die('Your file size is to large.');
    }
    else {
      //the file has passed the test
      //These files need to be included as dependencies when on the front end.
      require_once( ABSPATH . 'wp-admin/includes/image.php' );
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
      require_once( ABSPATH . 'wp-admin/includes/media.php' );
       
      // Let WordPress handle the upload.
      // Remember, 'upload' is the name of our file input in our form above.
      $file_id = media_handle_upload( 'upload', 0 );
 
      if ( is_wp_error( $file_id ) ) {
         wp_die('Error loading file!');
      } else {
        augustin_upload_pdf_for_processing(wp_get_attachment_url($file_id));
        wp_die('Your newspaper was succesfuly imported.');
      }
    }
  }
  else {
    //set that to be the returned message
    wp_die('Error: '.$_FILES['upload']['error']);
  }
}
