<?php
namespace aitsydney;
use aitsydney\Database;
class Product extends Database{
    public $products = array();
    public $category = null;

    public function __construct(){
        parent::__construct();
        if( isset($_GET['category_id'] ) ){
            $this -> category = $_GET['category_id'];
        }
    }
    public function getProducts(){
        $query = "
        SELECT 
        @product_id := listing.id AS product_id,
        listing.name,
        listing.address,
        listing.phone_number,
        listing.suburb,
        ( SELECT @image_id := product_image.image_id FROM product_image WHERE product_image.product_id = @product_id LIMIT 1 ) AS image_id,
        ( SELECT image_file_name FROM image WHERE image.image_id = @image_id ) AS image
        FROM listing
        INNER JOIN product_quantity
        ON listing.id = product_quantity.product_id
        ";

        if( isset( $this -> category ) ){
            $query = $query . 
            " " . 
            "
            INNER JOIN
            listing_category
            ON listing_category.Listing_id = listing.id
            WHERE listing_category.Category_id = ?
            ";
        }

        $statement = $this -> connection -> prepare( $query );

        if( isset( $this -> Category ) ){
            $statement -> bind_param( 'i', $this -> Category );
        }

        if( $statement -> execute() ){
            $result = $statement -> get_result();
            while( $row = $result -> fetch_assoc() ){
                array_push( $this -> products, $row );
            }
        }
        return $this -> products;
    }
    
}
?>