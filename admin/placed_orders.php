<?php

include '../components/connect.php';

include '../components/check_inactivity.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}


if (isset($_POST['update_payment'])) {
   $order_id = $_POST['order_id'];
   $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';

   if (empty($payment_status)) {
      $message[] = 'Please select order status.';
   } else {
      $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
      $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
      $update_payment->execute([$payment_status, $order_id]);
      $message[] = 'Payment status updated!';
   }
}


if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>placed orders</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

   <script>
      // Store scroll position before reload
      window.addEventListener('beforeunload', function() {
         sessionStorage.setItem('scrollPosition', window.scrollY);
      });

      // Restore scroll position after reload
      window.addEventListener('load', function() {
         const scrollPosition = sessionStorage.getItem('scrollPosition');
         if (scrollPosition !== null) {
            window.scrollTo(0, scrollPosition);
            sessionStorage.removeItem('scrollPosition'); // Clear stored position
         }
      });
   </script>


</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="orders">

      <?php
      $payment_status = isset($_GET['payment_status']) ? $_GET['payment_status'] : '';
      if ($payment_status === '') {
         echo '<h1 class="heading">All Placed Orders</h1>';
      } else {
         echo '<h1 class="heading">Orders ' . ucfirst($payment_status) . '</h1>';
      }
      ?>

      <div class="filter-container">
         <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by name or number..">
            <button onclick="searchOrders()">Search</button>
         </div>

         <div class="filter-dropdown">
            <label for="filterOptions">Sort By:</label>
            <select id="filterOptions" onchange="applyFilters()">
               <option value="latest">Latest</option>
               <option value="oldest">Oldest</option>
               <option value="arrival">Latest Arrival</option>
               <option value="lowToHigh">Price (low to high)</option>
               <option value="highToLow">Price (high to low)</option>
            </select>
         </div>
      </div>

      <div class="box-container">

         <?php
         $payment_status = isset($_GET['payment_status']) ? $_GET['payment_status'] : '';
         $select_orders = $payment_status === ''
            ? $conn->prepare("SELECT * FROM `orders` ORDER BY id DESC") // Load orders with highest order ID first
            : $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ? ORDER BY id DESC"); // Load filtered orders with highest order ID first

         if ($payment_status !== '') {
            $select_orders->execute([$payment_status]);
         } else {
            $select_orders->execute();
         }

         if ($select_orders->rowCount() > 0) {
            while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
               if ($fetch_orders['payment_status'] == 'processing' && $fetch_orders['seen'] == 0) {
                  $update_seen = $conn->prepare("UPDATE `orders` SET seen = ? WHERE id = ?");
                  $update_seen->execute([1, $fetch_orders['id']]);
               }
         ?>
               <div class="box">
                  <form action="" method="post" class="box-form">
                     <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                     <input type="hidden" name="total_price" value="<?= $fetch_orders['total_price']; ?>">
                     <input type="hidden" name="arrival_date" value="<?= $fetch_orders['arrival_date']; ?>">
                     <div class="box-details">
                        <p>placed on : <span><?= $fetch_orders['placed_on']; ?></span></p>
                        <p>reference no. : <span><?= $fetch_orders['reference_no']; ?></span></p>
                        <p>name : <span><?= $fetch_orders['fname']; ?> <?= $fetch_orders['lname']; ?></span></p>
                        <p>primary no. : <span><?= $fetch_orders['cnumber']; ?></span></p>
                        <p>add. no. : <span><?= $fetch_orders['number']; ?></span></p>
                        <p>contact method : <span><?= $fetch_orders['numtype']; ?></span></p>
                        <p>total products : <span><?= $fetch_orders['total_products']; ?></span></p>
                        <p>total price : <span>$<?= $fetch_orders['total_price']; ?></span></p>
                        <p>flight no. : <span><?= $fetch_orders['flight_num'] ? $fetch_orders['flight_num'] : 'Not specified'; ?></span></p>
                        <p>arrival date : <span><?= $fetch_orders['arrival_date'] ? $fetch_orders['arrival_date'] : 'Not specified'; ?></span></p>
                     </div>
                     <select name="payment_status" class="select">
                        <option selected disabled><?= $fetch_orders['payment_status']; ?></option>
                        <option value="processing">processing</option>
                        <option value="ready">ready</option>
                        <option value="completed">completed</option>
                     </select>
                     <div class="flex-btn">
                        <input type="submit" value="update" class="option-btn" name="update_payment">
                        <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">delete</a>
                     </div>
                  </form>
               </div>
         <?php
            }
         } else {
            echo '      <div class="empty-message">
            <p class="empty">no orders placed yet!</p>
         </div>';
         }
         ?>

      </div>

   </section>

   <script src="../js/admin_script.js"></script>

   <script>
      function searchOrders() {
         const searchInput = document.getElementById("searchInput");
         const searchTerm = searchInput.value.trim().toLowerCase();

         const orderBoxes = document.querySelectorAll(".box");
         orderBoxes.forEach(box => {
            const referenceElement = box.querySelector("p:nth-child(2) span");
            const nameElement = box.querySelector("p:nth-child(3) span");
            const numberElement = box.querySelector("p:nth-child(4) span");
            const reference = referenceElement.textContent.toLowerCase();
            const name = nameElement.textContent.toLowerCase();
            const number = numberElement.textContent.toLowerCase();


            if (reference.includes(searchTerm) || name.includes(searchTerm) || number.includes(searchTerm)) {
               box.style.display = "block";
            } else {
               box.style.display = "none";
            }
         });
      }


      function applyFilters() {
         var selectedOption = document.getElementById("filterOptions").value;
         var orderBoxes = document.querySelectorAll(".box-container .box");
         var sortedBoxes;

         switch (selectedOption) {
            case "oldest":
               sortedBoxes = Array.from(orderBoxes).sort((a, b) => {
                  var idA = parseInt(a.querySelector("input[name='order_id']").value);
                  var idB = parseInt(b.querySelector("input[name='order_id']").value);
                  return idA - idB;
               });
               break;
            case "arrival":
               sortedBoxes = Array.from(orderBoxes).sort((a, b) => {
                  var dateA = a.querySelector("input[name='arrival_date']").value;
                  var dateB = b.querySelector("input[name='arrival_date']").value;

                  if (dateA === "") {
                     return 1;
                  } else if (dateB === "") {
                     return -1;
                  } else {
                     return new Date(dateA) - new Date(dateB);
                  }
               });
               break;
            case "lowToHigh":
               sortedBoxes = Array.from(orderBoxes).sort((a, b) => {
                  var priceA = parseFloat(a.querySelector("input[name='total_price']").value);
                  var priceB = parseFloat(b.querySelector("input[name='total_price']").value);
                  return priceA - priceB;
               });
               break;
            case "highToLow":
               sortedBoxes = Array.from(orderBoxes).sort((a, b) => {
                  var priceA = parseFloat(a.querySelector("input[name='total_price']").value);
                  var priceB = parseFloat(b.querySelector("input[name='total_price']").value);
                  return priceB - priceA;
               });
               break;
            default:
               sortedBoxes = Array.from(orderBoxes).sort((a, b) => {
                  var idA = parseInt(a.querySelector("input[name='order_id']").value);
                  var idB = parseInt(b.querySelector("input[name='order_id']").value);
                  return idB - idA;
               });
         }

         var boxContainer = document.querySelector(".box-container");
         boxContainer.innerHTML = '';
         sortedBoxes.forEach(box => {
            boxContainer.appendChild(box);
         });

         adjustItemHeights();
      }

      function adjustItemHeights() {
         const container = document.querySelector('.box-container');
         const boxes = Array.from(container.querySelectorAll('.box'));

         let currentRow = [];
         let maxHeight = 0;

         boxes.forEach(box => {
            const boxTop = box.getBoundingClientRect().top;

            if (currentRow.length === 0 || boxTop === currentRow[0]) {
               currentRow.push(boxTop);
               maxHeight = Math.max(maxHeight, box.offsetHeight);
            } else {
               currentRow.forEach(rowTop => {
                  boxes.forEach(box => {
                     if (box.getBoundingClientRect().top === rowTop) {
                        box.style.height = `${maxHeight}px`;
                     }
                  });
               });

               currentRow = [boxTop];
               maxHeight = box.offsetHeight;
            }
         });

         currentRow.forEach(rowTop => {
            boxes.forEach(box => {
               if (box.getBoundingClientRect().top === rowTop) {
                  box.style.height = `${maxHeight}px`;
               }
            });
         });
      }

      window.addEventListener('load', () => {
         const container = document.querySelector('.box-container');
         const boxes = Array.from(container.querySelectorAll('.box'));

         let currentRow = [];
         let maxHeight = 0;

         boxes.forEach(box => {
            const boxTop = box.getBoundingClientRect().top;

            if (currentRow.length === 0 || boxTop === currentRow[0]) {
               currentRow.push(boxTop);
               maxHeight = Math.max(maxHeight, box.offsetHeight);
            } else {
               currentRow.forEach(rowTop => {
                  boxes.forEach(box => {
                     if (box.getBoundingClientRect().top === rowTop) {
                        box.style.height = `${maxHeight}px`;
                     }
                  });
               });

               currentRow = [boxTop];
               maxHeight = box.offsetHeight;
            }
         });

         currentRow.forEach(rowTop => {
            boxes.forEach(box => {
               if (box.getBoundingClientRect().top === rowTop) {
                  box.style.height = `${maxHeight}px`;
               }
            });
         });
      });
   </script>

</body>

</html>