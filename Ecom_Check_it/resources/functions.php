<?php

//Helper, with messages
function set_message($msg){
    if(!empty($msg)){
        $_SESSION['message'] = $msg;
    }else{
        $msg = "";
    }
}

function display_message(){
    if(isset($_SESSION['message'])){
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}

// Nuvedimas.
function redirect($location){
    header("Location: $location");
}

// SQL užklausa.
function query($sql){
    global $connection;
    return mysqli_query($connection, $sql);
}

// SQL klaidos aptikimas
function confirm($result){
    global $connection;
    if(!$result){
        die("Query failed!!! <br> " . mysqli_error($connection));
    }
}

// Tarpeliu naikinimas
function escape_string($sql){
    global $connection;
    return mysqli_real_escape_string($connection, $sql);
}

// Rezultatu paeimas
function fetch_array($result){
    return mysqli_fetch_array($result);
}

// gauti produktus
function get_products(){
    $query = query("SELECT * FROM products");
    confirm($query);

    $rows = mysqli_num_rows($query); // Get total of mumber of rows from the database

    if(isset($_GET['page'])){ //get page from URL if its there
        $page = preg_replace('#[^0-9]#', '', $_GET['page']);//filter everything but numbers
    } else{// If the page url variable is not present force it to be number 1
        $page = 1;
    }

    $perPage = 6; // Items per page here 
    $lastPage = ceil($rows / $perPage); // Get the value of the last page

    // Be sure URL variable $page(page number) is no lower than page 1 and no higher than $lastpage
    if($page < 1){ // If it is less than 1
        $page = 1; // force if to be 1
    }elseif($page > $lastPage){ // if it is greater than $lastpage
        $page = $lastPage; // force it to be $lastpage's value
    }

    $middleNumbers = ''; // Initialize this variable

    // This creates the numbers to click in between the next and back buttons
    $sub1 = $page - 1;
    $sub2 = $page - 2;
    $add1 = $page + 1;
    $add2 = $page + 2;

    if($page == 1){
        $middleNumbers .= '<li class="page-item active"><a>' .$page. '</a></li>';
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$add1.'">' .$add1. '</a></li>';
    }elseif ($page == $lastPage) {
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$sub1.'">' .$sub1. '</a></li>';
        $middleNumbers .= '<li class="page-item active"><a>' .$page. '</a></li>';
    }elseif ($page > 2 && $page < ($lastPage -1)) {
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$sub2.'">' .$sub2. '</a></li>';
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$sub1.'">' .$sub1. '</a></li>';
        $middleNumbers .= '<li class="page-item active"><a>' .$page. '</a></li>';
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$add1.'">' .$add1. '</a></li>';
        $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$add2.'">' .$add2. '</a></li>';
    } elseif($page > 1 && $page < $lastPage){
       $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page= '.$sub1.'">' .$sub1. '</a></li>';
       $middleNumbers .= '<li class="page-item active"><a>' .$page. '</a></li>';
       $middleNumbers .= '<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$add1.'">' .$add1. '</a></li>';
    }
  
    // page2-12 page1-6 , page3-18, page2-6
    $limit = 'LIMIT ' . ($page-1) * $perPage . ',' . $perPage;

    $query2 = query(" SELECT * FROM products $limit");
    confirm($query2);
    
    $outputPagination = ""; // Initialize the pagination output variable

    if($page != 1){
        $prev  = $page - 1;
        $outputPagination .='<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$prev.'">Back</a></li>';
    }

    $outputPagination .= $middleNumbers;

    // If we are not on the very last page we the place the next link
    if($page != $lastPage){
        $next = $page + 1;
        $outputPagination .='<li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?page='.$next.'">Next</a></li>';
    }

    while($row = fetch_array($query2)){

        $product_image = display_image($row['product_image']);

        $product = <<<DELIMETER

        <div class='col-sm-4 col-lg-4 col-md-4'>
            <div class='thumbnail'>
                <a href="item.php?id={$row['product_id']}"><img  src="../resources/{$product_image}" alt=''></a>
                <div class='caption'>
                    <h4 class='pull-right'>{$row['product_price']}&euro;</h4>
                    <h4>
                        <a href='item.php?id={$row['product_id']}'>{$row['product_title']}</a>
                    </h4>
                    <p>
                        {$row['product_description']}
                    </p>
                    <a class='btn btn-primary' target='_self' href='../resources/cart.php?add={$row['product_id']}'>Add</a>
                </div>
            </div>
        </div>

        DELIMETER;

        echo $product;
    }

    echo "<div style='clear:both' class='text-center'><ul class='pagination'>{$outputPagination}</ul></div>";

}

function get_categories(){
    $query = query("SELECT * FROM categories");
    confirm($query);

    // Informacijos spausdinimas.
    while($row = fetch_array($query)){
        $category_links = <<<DELIMETER
            <a href='category.php?id={$row['cat_id']}' class='list-group-item'>{$row['cat_title']}</a>
        DELIMETER;

        echo $category_links;
    }
}

function get_categories_product_by_id($id){
    $query = query("SELECT * FROM products WHERE product_category_id = " .$id. " ");
    confirm($query);

    while($row = fetch_array($query)){
        $product = <<<DELIMETER

            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <img src="{$row['product_image']}" alt="">
                    <div class="caption">
                        <h3>{$row['product_title']}</h3>
                        <p>{$row['short_desc']}</p>
                        <p>
                            <a href="#" class="btn btn-primary">Buy Now!</a> <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                        </p>
                    </div>
                </div>
            </div>

        DELIMETER;

        echo $product;
    }
}


function get_products_in_shop(){
    $query = query("SELECT * FROM products ");
    confirm($query);

    while($row = fetch_array($query)){
        $product = <<<DELIMETER

            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <img src="../resources/images/{$row['product_image']}" alt="">
                    <div class="caption">
                        <h3>{$row['product_title']}</h3>
                        <p>{$row['short_desc']}</p>
                        <p>
                            <a class='btn btn-primary' target='_self' href='../resources/cart.php?add={$row['product_id']}'>Add</a>
                            <a href="item.php?id={$row['product_id']}" class="btn btn-default">More Info</a>
                        </p>
                    </div>
                </div>
            </div>

        DELIMETER;

        echo $product;
    }
}

function login_user(){
    if(isset($_POST['submit'])){
        $username = escape_string($_POST['username']);
        $password = escape_string($_POST['password']);

        $query = query("SELECT * FROM users WHERE username= '{$username}' AND password='".$password."' ");
        confirm($query);

        if(mysqli_num_rows($query) == 0){
            set_message("Duomenys yra neteisingi");
            redirect('login.php');
        }else{
            $_SESSION['username'] = $username;
            set_message("Sveiki, čia yra Admin. skydelis " . $username);
            redirect('admin');
        }
    }
}

function send_message(){
    if(isset($_POST['submit'])){

        $to = "gedas97@gmail.com";

        $from_name = $_POST['name'];
        $subject = $_POST['subject'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        $header = "From: {$from_name} {$email}";

        $result = mail($to, $subject, $subject, $header);

        if(!$result){
            set_message("Sorry, there was an error");
            redirect("contact.php");
        }else{
            set_message("Your message has benn sent");
            redirect("contact.php");
        }
    }
}

function last_id(){
    global $connection;

    return mysqli_insert_id($connection);
}

function display_orders(){

    $query = query("SELECT * FROM orders");
    confirm($query);
    
    while($row = fetch_array($query)) {
    
        $orders = <<<DELIMETER
        
        <tr>
            <td>{$row['order_id']}</td>
            <td>{$row['order_amount']}</td>
            <td>{$row['order_transaction']}</td>
            <td>{$row['order_currency']}</td>
            <td>{$row['order_status']}</td>
            <td><a class="btn btn-danger" href="../../resources/templates/back/delete_order.php?id={$row['order_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
        </tr>
        
        DELIMETER;
        
        echo $orders;

    }
}

function display_users() {

    $category_query = query("SELECT * FROM users");
    confirm($category_query);
    
    while($row = fetch_array($category_query)) {
    
        $user_id = $row['user_id'];
        $username = $row['username'];
        $email = $row['email'];
        $password = $row['password'];
        //$product_image = display_image($row['user_photo']);
        $user_photo = $row['user_photo'];
        
            $user = <<<DELIMETER
            <tr>
                <td>{$user_id}</td>
                <td>{$username}</br>
                    <img width='100' src="../../resources/images/{$user_photo}" alt="">
                </td>
                <td>{$email}</td>
                <td><a class="btn btn-danger" href="../../resources/templates/back/delete_user.php?id={$row['user_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
            </tr>
        DELIMETER;
        
        echo $user;

    }
}
$upload_directory = "images";
function display_image($picture) {

    global $upload_directory;
    return $upload_directory  . "/" . $picture;
    
}

function get_products_in_admin(){

    $query = query(" SELECT * FROM products");
    confirm($query);
    
    while($row = fetch_array($query)) {
    
        $category = show_product_category_title($row['product_category_id']);
        $product_image = display_image($row['product_image']);

        $product = <<<DELIMETER
        
                <tr>
                    <td>{$row['product_id']}</td>
                    <td>{$row['product_title']}</br>
                        <a href="index.php?edit_product&id={$row['product_id']}"><img width='100' src="../../resources/{$product_image}" alt=""></a>
                    </td>
                    <td>{$category}</td>
                    <td>{$row['product_price']}</td>
                    <td>{$row['product_quantity']}</td>
                    <td><a class="btn btn-danger" href="../../resources/templates/back/delete_product.php?id={$row['product_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
                </tr>
        
        DELIMETER;
        
        echo $product;
    
    }
}

function show_product_category_title($product_category_id){

    $category_query = query("SELECT * FROM categories WHERE cat_id = '{$product_category_id}' ");
    confirm($category_query);
    
    while($category_row = fetch_array($category_query)) {
    
        return $category_row['cat_title'];
    }
}


function add_product() {

    if(isset($_POST['publish'])) {
    
    
        $product_title          = escape_string($_POST['product_title']);
        $product_category_id    = escape_string($_POST['product_category_id']);
        $product_price          = escape_string($_POST['product_price']);
        $product_description    = escape_string($_POST['product_description']);
        $short_desc             = escape_string($_POST['short_desc']);
        $product_quantity       = escape_string($_POST['product_quantity']);
        $product_image          = $_FILES['file']['name'];
        $image_temp_location    = $_FILES['file']['tmp_name'];
          
        //If there will be a error, php.ini needs to be change upload_max side and so on.
        $moved = move_uploaded_file($image_temp_location  ,  UPLOAD_DIRECTORY.DS. $_FILES["file"]["name"]);

        $query = query("INSERT INTO products(product_title, product_category_id, product_price, product_description, short_desc, product_quantity, product_image) 
            VALUES('{$product_title}', '{$product_category_id}', '{$product_price}', '{$product_description}', '{$short_desc}', '{$product_quantity}', '{$product_image}')
        ");

        $last_id = last_id();

        confirm($query);

        set_message("New Product with id {$last_id} was Added");
        redirect("index.php?products");
    }
    
}

function show_categories_add_product_page(){

    $query = query("SELECT * FROM categories");
    confirm($query);
    
    while($row = fetch_array($query)) {
      
        $categories_options = <<<DELIMETER
            <option value="{$row['cat_id']}">{$row['cat_title']}</option>
        DELIMETER;
        echo $categories_options;
        
    }

}

function show_categories_in_admin() {

    $category_query = query("SELECT * FROM categories");
    confirm($category_query);
    
    while($row = fetch_array($category_query)) {
    
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];
        
        
        $category = <<<DELIMETER
            <tr>
                <td>{$cat_id}</td>
                <td>{$cat_title}</td>
                <td><a class="btn btn-danger" href="../../resources/templates/back/delete_category.php?id={$row['cat_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
            </tr>
        DELIMETER;
        
        echo $category;
    
    }
}

function add_category() {
    global $connection;
    if(isset($_POST['add_category'])) {

        $cat_title = escape_string($_POST['cat_title']);
    
        if(empty($cat_title) || $cat_title == " ") {
    
            echo "<h3 class='bg-danger'>THIS CANNOT BE EMPTY</h3>";

        } else {
    
            $insert_cat = query("INSERT INTO categories(cat_title) VALUES('{$cat_title}') ");
            confirm($insert_cat);

            set_message("Category Created");

        }
    }
}
    
function add_user() {

    if(isset($_POST['add_user'])) {
    
        $username   = escape_string($_POST['username']);
        $email      = escape_string($_POST['email']);
        $password   = escape_string($_POST['password']);
        $user_photo = $_FILES['file']['name'];
        $photo_temp = $_FILES['file']['tmp_name'];

        move_uploaded_file($photo_temp, UPLOAD_DIRECTORY . DS . $user_photo);

   

        $query = query("INSERT INTO users(username,email,`password`,user_photo) VALUES('{$username}','{$email}','{$password}','{$user_photo}')");
        confirm($query);
        
        set_message("USER CREATED");
        
        redirect("index.php?users");

    }  
}

function get_reports(){

    $query = query(" SELECT * FROM reports");
    confirm($query);
    
    while($row = fetch_array($query)) {
    
        $report = <<<DELIMETER
        
                <tr>
                    <td>{$row['report_id']}</td>
                    <td>{$row['product_id']}</td>
                    <td>{$row['order_id']}</td>
                    <td>{$row['product_price']}</td>
                    <td>{$row['product_title']}
                    <td>{$row['product_quantity']}</td>
                    <td><a class="btn btn-danger" href="../../resources/templates/back/delete_report.php?id={$row['report_id']}"><span class="glyphicon glyphicon-remove"></span></a></td>
                </tr>
        
        DELIMETER;
        
        echo $report;

    }   
}
//**********************************************************************/
function add_slides(){

    if(isset($_POST['add_slide'])) {

        $slide_title        = escape_string($_POST['slide_title']);
        $slide_image        = ($_FILES['file']['name']);
        $slide_image_loc    = ($_FILES['file']['tmp_name']);
        
        if(empty($slide_title) || empty($slide_image)) {

            echo "<p class='bg-danger'>This field cannot be empty</p>";
        
        } else {

            move_uploaded_file($slide_image_loc, UPLOAD_DIRECTORY . DS . $slide_image);
            
            $query = query("INSERT INTO slides(slide_title, slide_image) VALUES('{$slide_title}', '{$slide_image}')");
            confirm($query);
            set_message("Slide Added");
            redirect("index.php?slides");

        }
    }
}

function get_current_slide_in_admin(){

    $query = query("SELECT * FROM slides ORDER BY slide_id DESC LIMIT 1");
    confirm($query);

    while($row = fetch_array($query)) {
        $slide_image = display_image($row['slide_image']);
        $slide_active_admin = <<<DELIMETER
            <img class="img-responsive rightPhoto" src="../../resources/{$slide_image}" alt="">
        DELIMETER;
        echo $slide_active_admin;
    }
}

function get_active(){

    $query = query("SELECT * FROM slides ORDER BY slide_id DESC LIMIT 1");
    confirm($query);
    
    while($row = fetch_array($query)) { 
        $slide_image = display_image($row['slide_image']);
        $slide_active = <<<DELIMETER
            <div class="item active">
                <img class="slide-image" src="../resources/{$slide_image}" alt="">
            </div>
        DELIMETER;
        
        echo $slide_active;
        
        
    }
}

function get_slides() {

    $query = query("SELECT * FROM slides");
    confirm($query);

    while($row = fetch_array($query)) {

        $slide_image = display_image($row['slide_image']);
        //var_dump(__FILE__);
        //var_dump("../../resources/{$slide_image}");exit;

        $slides = <<<DELIMETER

            <div class="item">
                <img class="slide-image" src="../resources/{$slide_image}" alt="">
            </div>

        DELIMETER;
        echo $slides;
    }
}

function get_slide_thumbnails(){

    $query = query("SELECT * FROM slides ORDER BY slide_id ASC ");
    confirm($query);
    
    while($row = fetch_array($query)) {
        $slide_image = display_image($row['slide_image']);
        $slide_thumb_admin = <<<DELIMETER
            <div class="col-xs-6 col-md-3 image_container">
                <a href="index.php?delete_slide={$row['slide_id']}">
                    <img  class="img-responsive slide_image" src="../../resources/{$slide_image}" alt="">
                </a>
                <div class="caption">
                    <p>{$row['slide_title']}</p>
                </div>
            </div>
        DELIMETER;
        
        echo $slide_thumb_admin;
    }    
}

?>