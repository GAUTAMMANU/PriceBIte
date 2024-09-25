<?php
  include("..\database.php");
?>
<?php
    $sql = "SELECT sval FROM cart order by orderid desc limit 1";
    $swiggyresult = $db->query($sql);
    $swiggyValue = $swiggyresult->fetch_assoc()['sval']; // Fetch the value from the result

    $sql = "SELECT mval FROM cart order by orderid desc limit 1";
    $magicresult = $db->query($sql);
    $magicValue = $magicresult->fetch_assoc()['mval'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PriceBite</title>
    <link rel="stylesheet" href="main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <style>  
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="index.html" class="navbar-logo h-font">PriceBite</a>
        </div>

        <div class="navbar-right">
        </div>
    </nav>

    <div class="section">
        <div class="swiggy" id="swiggy">
            <h1 class="h-font">Swiggy Cart</h1>
            <img src="images/company/cartSwiggy.png" class="cartimg">
            <p class="cart-Value cart-Value1"></p>
        </div>
        <div class="magicpin" id="magicpin">
            <h1 class="h-font">MagicPin</h1>
            <img src="images/company/blueCart.png" class="cartimg2">
            <br>
            <p class="cart-Value cart-Value2"></p>
        </div>
    </div>
    <center>
        <!-- <button onclick="checkValue()" class="btn-result">Result</button> -->

    </center>
    <br>

    <div class="footer">
        <div class="address">
            <div class="brand">PriceBite</div>
            <div class="line-1">CL02-Lab, ABB-3</div>
            <div class="line-2">Jaypee Institute of information Technology</div>
            <div class="line-3">Noida, Sector-62</div>
        </div>
        <div class="socials">
            <br>
            <div class="link-1">Instagram</div>
            <div class="link-2">Facebook</div>
            <div class="link-3">Twitter</div>
        </div>
    </div>
</body>
<script>
  if (typeof <?= json_encode($swiggyValue) ?> !== 'undefined' && typeof <?= json_encode($magicValue) ?> !== 'undefined') {
    document.querySelector('.cart-Value1').innerText ='₹' + <?= json_encode($swiggyValue) ?>;
    document.querySelector('.cart-Value2').innerText ='₹' + <?= json_encode($magicValue) ?>;
    
    if (<?= json_encode($swiggyValue) ?> < <?= json_encode($magicValue) ?>) {
        triggerConfetti();
        swiggy.classList.add('winner');
    } else {
        triggerConfetti();
        magicpin.classList.add('winner');
    }
}

  function triggerConfetti() {
    const duration = 1 * 1000; // 3 seconds
    confetti({
      particleCount: 100,
      spread: 160,
      origin: { y: 0.6 },
      ticks: 200,
      disableForReducedMotion: true,
      shapes: ['circle', 'square'],
    });
  }
  </script>
</html>
