<?php
namespace aitsydney;
use aitsydney\Database;
class WishList extends Database{
  private $account_id;
  private $wishlist_id;
  public function __construct(){
    parent::__construct();
    $this -> account_id = $this -> getAccountId();
    $this -> wishlist_id = $this -> getListId();
  }
  public function getAccountId(){
    // initialise session
    if( session_status() == PHP_SESSION_NONE ){
      session_start();
    }
    $account_id = ( isset($_SESSION['auth'])) ? $_SESSION['auth'] : null ;
    return $account_id;
  }
  
  public function getListId(){
    $account_id = $this -> getAccountId();
    // the user is logged in
    if( $account_id ){
      // find the user's list
      $find_query = "SELECT wishlist_id FROM wishlist WHERE account_id = UNHEX( ? ) ";
      $statement = $this -> connection -> prepare( $find_query );
      $statement -> bind_param( 's', $account_id );
      if( $statement -> execute() ){
        $result = $statement -> get_result();
        if( $result -> num_rows == 0 ){
          //user list not in database
          // create the list
          $list_id = $this -> createNewList( $account_id );
          return $list_id;
        }
        else{
          $row = $result -> fetch_assoc();
          return $row['wishlist_id'];
        }
      }
    }
    // the user is not logged in
    {
      return null;
    }
  }
  private function createNewList( $account_id ){
    $create_query = "INSERT INTO wishlist (account_id) VALUES( UNHEX(?) )";
    $statement = $this -> connection -> prepare( $create_query );
    $statement -> bind_param('s', $account_id );
    $statement -> execute();
    $wishlist_id = $this -> connection -> insert_id;
    return $wishlist_id;
  }
  public function setItem( $product_id ){
  }
}
?>