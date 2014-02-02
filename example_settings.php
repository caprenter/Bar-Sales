<?php
//Database connection info
$database_name = "my_bar";
$host = "localhost";
$database_user = "username";
$database_password = "password";

//Domain info
$domain = "your-domain-here";
$site_name = "My Bar";

//Wine ID's
/*The bar on which this application is based sells wine in small, medium
 * and large glasses. The information here maps those sizes to the 
 * corresponding database ids in the stock table so that we can work 
 * out when a whole bottle has been used up.
*/
//Glasses
$white_wine_ids = array("small" => 17,
                        "medium" => 18,
                        "large" => 16
                        );
$white_wine_bottle_id = 22;
$red_wine_ids = array("small" => 19,
                      "medium" => 20,
                      "large" => 21
                      );
$red_wine_bottle_id = 23;
?>
