<?php

if(isset($_POST['add_to_wishlist'])){

   if(isset($_COOKIE['session_id'])){
      $session_id = $_COOKIE['session_id'];
   }else{
      $session_id = uniqid();
      setcookie('session_id', $session_id, time() + (86400 * 30), "/"); // set cookie to expire in 30 days
   }

      $pid = $_POST['pid'];
      $pid = filter_var($pid, FILTER_SANITIZE_STRING);
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $price = $_POST['price'];
      $price = filter_var($price, FILTER_SANITIZE_STRING);
      $image = $_POST['image'];
      $image = filter_var($image, FILTER_SANITIZE_STRING);

      $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND session_id = ?");
      $check_wishlist_numbers->execute([$name, $session_id]);

      $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND session_id = ?");
      $check_cart_numbers->execute([$name, $session_id]);

      if($check_wishlist_numbers->rowCount() > 0){
         $message[] = 'already added to wishlist!';
      }elseif($check_cart_numbers->rowCount() > 0){
         $message[] = 'already added to cart!';
      }else{
         $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(session_id, pid, name, price, image) VALUES(?,?,?,?,?)");
         $insert_wishlist->execute([$session_id, $pid, $name, $price, $image]);
         $message[] = 'added to wishlist!';
      }

}

if(isset($_POST['add_to_cart'])){

   if(isset($_COOKIE['session_id'])){
      $session_id = $_COOKIE['session_id'];
   }else{
      $session_id = uniqid();
      setcookie('session_id', $session_id, time() + (86400 * 30), "/"); // set cookie to expire in 30 days
   }

      $pid = $_POST['pid'];
      $pid = filter_var($pid, FILTER_SANITIZE_STRING);
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $price = $_POST['price'];
      $price = filter_var($price, FILTER_SANITIZE_STRING);
      $image = $_POST['image'];
      $image = filter_var($image, FILTER_SANITIZE_STRING);
      $qty = $_POST['qty'];
      $qty = filter_var($qty, FILTER_SANITIZE_STRING);

      $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND session_id = ?");
      $check_cart_numbers->execute([$name, $session_id]);

      if($check_cart_numbers->rowCount() > 0){
         $message[] = 'already added to cart!';
      }else{

         $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND session_id = ?");
         $check_wishlist_numbers->execute([$name, $session_id]);

         if($check_wishlist_numbers->rowCount() > 0){
            $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND session_id = ?");
            $delete_wishlist->execute([$name, $session_id]);
         }

         $insert_cart = $conn->prepare("INSERT INTO `cart`(session_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
         $insert_cart->execute([$session_id, $pid, $name, $price, $qty, $image]);
         $message[] = 'added to cart!';
         
      }

}

?>