<?php
    require_once("config.php");
?>

<?php
    if(isset($_GET['add'])){

        $query = query("SELECT * FROM products WHERE product_id='". escape_string($_GET['add']) ."' ");

        confirm($query);

        while($row = fetch_array($query)){
            if($row['product_quantity'] != $_SESSION['product_' . $_GET['add']]){
                
                // Kiekviena karta prideda po 1 paspaudus. 
                $_SESSION['product_' . $_GET['add']] += 1;
                redirect("../public/checkout.php");

            }else{

                set_message("We have only " . $row['product_quantity'] . " " . " available");
                redirect("../public/checkout.php");

            }
        }
    }

    if(isset($_GET['remove'])) {
        // Product_id and it makes -1 if we press
        $_SESSION['product_' . $_GET['remove']]--;
    
        // If lower than 1 we unset
        if($_SESSION['product_' . $_GET['remove']] < 1) {
    
            unset($_SESSION['item_total']);
            unset($_SESSION['item_quantity']);
            redirect("../public/checkout.php");
    
        } else {
    
            redirect("../public/checkout.php");
    
        }
    }

    if(isset($_GET['delete'])) { 

        $_SESSION['product_' . $_GET['delete']] = '0';
        unset($_SESSION['item_total']);
        unset($_SESSION['item_quantity']);
      
        redirect("../public/checkout.php");
      
    }

    function cart(){
        //
        //    foreach ($_SESSION as $name => $value) {
        //
        //        echo "<pre>";
        //          var_dump($_SESSION);
        //        echo "</pre>";
        //
        //
        //    }

        $total = 0;
        $item_quantity = 0;
        $item_name = 1;
        $item_number = 1;
        $amount = 1;
        $quantity = 1;
        foreach($_SESSION as $name => $value){
            if($value > 0){
                //echo $name;
                // First 0 , takes like 1 and works until 7, so that means 0-6
                if(substr($name,0, 8) == "product_"){
                    // We take the full name.
                    $length = strlen($name);
                    //echo "</br>" . $length;
                    // We take from 8 til the $length in this way
                    // we will take the ID
                    $id = substr($name, 8 , $length);

                    $query = query("SELECT * FROM products WHERE product_id='".escape_string($id)."' ");
                    
                    confirm($query);
            
                    while($row = fetch_array($query)){

                        $sub = $row['product_price']*$value;
                        $item_quantity +=$value;

                        $product = <<<DELIMETER
            
                        <tr>
                            <td>{$row['product_title']}</td>
                            <td>{$row['product_price']} €</td>
                            <td>{$value}</td>
                            <td>{$sub}</td>
                            <td>
                                <a class="btn btn-warning" href="../resources/cart.php?remove={$row['product_id']}"><span class="glyphicon glyphicon-minus"></span></a>
                                <a class="btn btn-success" href="../resources/cart.php?add={$row['product_id']}"><span class="glyphicon glyphicon-plus"></span></a>
                                <a class="btn btn-danger" href="../resources/cart.php?delete={$row['product_id']}"><span class="glyphicon glyphicon-remove"></span></a>
                            </td>
                        </tr>

                    
                        <input type="hidden" name="item_name_{$item_name}" value="{$row['product_title']}">
                        <input type="hidden" name="item_number_{$item_number}" value="{$row['product_id']}">
                        <input type="hidden" name="amount_{$amount}" value="{$row['product_price']}">
                        <input type="hidden" name="quantity_{$quantity}" value="{$row['product_quantity']}">
            
                        DELIMETER;
            
                        echo $product;

                        $item_name++;
                        $item_number++;
                        $amount++;
                        $quantity++;

                       
                    }

                    $_SESSION['item_total'] = $total += $sub;
                    $_SESSION['item_quantity'] = $item_quantity;
                }
            }
        }
    }

    function show_paypal(){
        if(isset($_SESSION['item_quantity']) && $_SESSION['item_quantity'] >= 1) {
            $paypal_button = <<<DELIMETER

                <input type="submit" name="submit" border="0">

            DELIMETER;

            return $paypal_button;
        }
    }

    function report(){
        if(isset($_POST['submit'])){

            global $connection;

            //foreach($_POST as $value => $something){

                //if(substr($value,0,-2) == "amount"){
                            
                    $amount = $_SESSION['item_total'];
                    $currency = "USD";
                    $digits=3;
                    $transaction = rand(pow(10, $digits-1), pow(10, $digits)-1);
                    $status = "Completed";
            
                    $send_order = query("INSERT INTO 
                            orders (order_amount, order_transaction, order_status, order_currency )
                        VALUES('{$amount}', '{$transaction}','{$status}','{$currency}')
                    ");

                    

                    // echo "<pre>";
                    //     var_dump($last_id);
                    // echo "</pre>";
                    // exit;


                    confirm($send_order);
                    $last_id = last_id();
                //}
            //}
            
            $total = 0;
            $item_quantity = 0;

            foreach($_SESSION as $name => $value){
                
                if($value > 0){
                
                    if(substr($name,0, 8) == "product_"){

                        $length = strlen($name);

                        $id = substr($name, 8 , $length);

                        $query = query("SELECT * FROM products WHERE product_id='".escape_string($id)."' ");
                        
                        confirm($query);
                
                        while($row = fetch_array($query)){

                            $sub = $row['product_price']*$value;
                            $item_quantity +=$value;

                            $product_price = $row['product_price'];
                            $product_title = $row['product_title'];
                            $insert_report = query("INSERT INTO 
                                    reports (product_id, order_id, product_price, product_title, product_quantity )
                                VALUES('{$id}', '{$last_id}', '{$product_price}','{$product_title}','{$value}')
                            ");

                        }
                    }
                }
            }
        }else{

            redirect('index.php');
    
        }
    }
?>