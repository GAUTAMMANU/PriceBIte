<?php
    include("database.php");
?>
<?php
require_once('C:/xampp/htdocs/drivertest/vendor/autoload.php');

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverWait;

$host = 'http://localhost:4444/wd/hub'; // Selenium server URL
  $capabilities = \Facebook\WebDriver\Remote\DesiredCapabilities::chrome();

  // Set additional browser options
  $options = new \Facebook\WebDriver\Chrome\ChromeOptions();
  $options->addArguments(['--incognito','--start-maximized']);

  // Set the executable path of Chrome
  $options->setBinary('C:/Program Files/Google/Chrome/Application/chrome.exe');

  $capabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);

if (isset($_POST["phno"])) {
  // Start the WebDriver session
  $host = 'http://localhost:4444/wd/hub'; // Selenium server URL

  $driver = RemoteWebDriver::create($host, $capabilities);

  // Check if it's a phone number submission

  $ph = $_POST["phno"];
  //echo "Received phone number: " . $ph . PHP_EOL;
  // Simulate phone number entry and submit
  $driver->get('https://www.swiggy.com/checkout');

  
  $sql = "SELECT * FROM User WHERE Phoneno = '$ph'";
  $result = $db->query($sql);
  if ($result->num_rows == 0 || $result->num_rows<0 ){
    createTable($ph,$db);
    $sql = "INSERT INTO User (Phoneno) VALUES ('$ph')";
    $db->query($sql);
  }
  $wait = new WebDriverWait($driver, 10);
  $driver->findElement(WebDriverBy::cssSelector('#root > div._3arMG > header > div > div > ul > li:nth-child(1) > div > span:nth-child(2)'))->click();
  $mobileInput = $driver->findElement(WebDriverBy::cssSelector('#mobile'));
  $mobileInput->sendKeys($ph);

  $driver->findElement(WebDriverBy::cssSelector('#overlay-sidebar-root > div > div > div._3vi_e > div > div > div > form > div._25qBi._2-hTu > a'))->click();
  $sessionId = $driver->getSessionID();
  echo json_encode(["sessionId" => $sessionId]);
  exit;
}

//Check if it's an OTP submission
if (isset($_POST["acc_otp"])) {
  $theotp = $_POST["acc_otp"];
  //echo "Received OTP: " . $theotp . PHP_EOL;
  //echo "receive sessionid: ".$_POST["sessionId"] . PHP_EOL;
  $sessionId = $_POST["sessionId"];

  $driver = RemoteWebDriver::createBySessionID($sessionId, $host);
  //$driver = $sessionId;
  $wait = new WebDriverWait($driver, 10);

    // Simulate OTP entry and submit
  $wait->until(
      WebDriverExpectedCondition::presenceOfElementLocated(
          WebDriverBy::cssSelector("#otp")
      )
  );

  $driver->wait()->until(function ($driver) use ($theotp) {
      $otpInput = $driver->findElement(WebDriverBy::cssSelector("#otp"));
      $otpInput->clear();
      $otpInput->sendKeys($theotp);
      return strlen($theotp) === 6;
  });

  $driver->findElement(WebDriverBy::cssSelector('#overlay-sidebar-root > div > div > div._3vi_e > div > div > div > div:nth-child(2) > form > div:nth-child(2) > div._25qBi._2-hTu > a'))->click();

  // Wait for the cart value to be displayed
  $wait->until(
      WebDriverExpectedCondition::presenceOfElementLocated(
          WebDriverBy::cssSelector("#root > div._3arMG > div > div > div._2sMsA > div > div.ZBf6d > div._3ZAW1")
      )
  );

  $foodItemsAndQuantities=array();
  $foodItems = [];
  $quantities = [];

  // Get the elements containing the food items and their respective quantities
  $foodItemElements = $driver->findElements(WebDriverBy::cssSelector('._33KRy'));
  $quantityElements = $driver->findElements(WebDriverBy::cssSelector('._2zAXs'));

  foreach ($foodItemElements as $foodItemElement) {
    $foodItemName = $foodItemElement->getText();
    $foodItemName = preg_replace('/[^A-Za-z0-9\s+]+/', '', $foodItemName);
    $pos = strpos($foodItemName, "\nCustomize");
    if ($pos !== false) {
        $foodItemName = substr($foodItemName, 0, $pos);
    }
    if(strpos($foodItemName,"Free")){
      continue;
    }
    $foodItems[] = $foodItemName;
  }

  foreach ($quantityElements as $quantityElement) {
    $quantities[] = $quantityElement->getText();
  }

  while (count($foodItems) > count($quantities)) {
    array_pop($foodItems);
  }


  foreach ($foodItems as $index => $foodItems) {
    $foodItemsAndQuantities[$foodItems] = $quantities[$index];
  }

  $restaurantName = $driver->findElements(WebDriverBy::cssSelector('#root > div._3arMG > div.nDVxx._340-t > div > div._2sMsA > div > button > span.u1PgV > div.V7Usk'));
  $restaurantLocation = $driver->findElements(WebDriverBy::cssSelector('#root > div._3arMG > div.nDVxx._340-t > div > div._2sMsA > div > button > span.u1PgV > div._2ofXa'));
  $restaurantName = $restaurantName[0]->getText();
  $restaurantLocation = $restaurantLocation[0]->getText();
  
  $phnoss=$_POST["phnos"];
  $result = $db->query("select Sid from User where Phoneno='$phnoss';");
  $row = $result->fetch_assoc();
  $usersid = $row['Sid'];

  foreach ($foodItemsAndQuantities as $foodItem => $quantity) {
    $sql = "INSERT INTO `Swiggy_order_$phnoss`(Sid,Rest_name, Rest_loc, orderid, item, quantity) VALUES ('$usersid','$restaurantName', '$restaurantLocation', CURRENT_TIMESTAMP(), '$foodItem', '$quantity')";
    $db->query($sql);
  }

  //customise code

  //   $customitem=[];
    
  //   $customise_btn=$driver->findElement(WebDriverBy::cssSelector('div._3SG03 > div > button'));
  //   foreach ($customise_btn as $btn) {
  //   $btn->click();
  //   $modalPlaceholderElement = $driver->findElement(WebDriverBy::cssSelector('#modal-placeholder > div > div > div._1Kr-y._3EeZR > div > div._2rqLb > div > div._8uDK4 > div'));
  //   $anchorElements = $modalPlaceholderElement->findElements(WebDriverBy::cssSelector('a._1gHSS'));
  //   $idNames = [];
  //   foreach ($anchorElements as $anchorElement) {
  //     $idName = $anchorElement->getAttribute('href');
  //     $idNames[] = $idName;
  //   }

  //   foreach ($idNames as $idName) {
  //   // Find the element with the specified id
  //     $custIdElement = $driver->findElement(WebDriverBy::cssSelector($idName));

  //     // Find all the checkbox input elements within the custIdElement
  //     $addOnElement = $driver->findElement(WebDriverBy::cssSelector('. _1JpoK'));
  //     // Get the text of the add-on element
  //     $addOnText = $addOnElement->getText();

  //     $checkboxElements = $custIdElement->findElements(WebDriverBy::cssSelector('input[type="checkbox"]'));

  //     // Check each checkbox
  //     $itemlist=[];
  //     foreach ($checkboxElements as $checkboxElement) {
  //         if ($checkboxElement->isSelected()) {
  //             // Get the name of the checked item using the label element
  //             $itemNameElement = $checkboxElement->findElement(WebDriverBy::xpath('following-sibling::label//span[@class="_2OGeA"]'));
  //             $itemName = $itemNameElement->getText();
  //             $itemlist[]=$itemname;
  //             //echo "Checked item name for " . $idName . ": " . $itemName . PHP_EOL;
  //             break; // Stop checking once a checked checkbox is found
  //         }
  //     }
  //     $customitem[$addOnText]=$itemlist;
  // }
  // $driver->findElement(WebDriverBy::cssSelector('#modal-placeholder > div > div > div._1Kr-y._3EeZR > div > div._1EZLh > div > button'))->click();
  // }
  

  $data = [
    "restaurantName" => $restaurantName,
    "restaurantLocation" => $restaurantLocation,
    "foodItemsAndQuantities" => $foodItemsAndQuantities,
    //"customitem" => $customitem
  ];
  
// Convert data to JSON
  $jsonData = json_encode($data);
  file_put_contents('swiggy_cart_data.json', $jsonData);
  // Extract the Swiggy Cart Value
  $cartValue = $driver->findElement(WebDriverBy::cssSelector("#root > div._3arMG > div > div > div._2sMsA > div > div.ZBf6d > div._3ZAW1"))->getText();
  
  $sql ="insert into Cart(Sid,sval) values('$usersid','$cartValue');";
  $db->query($sql);
  
  echo json_encode([
    "cartValue" => $cartValue,
    "foodItemsAndQuantities"=>$foodItemsAndQuantities
  ]);
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swiggy Login and Cart Value Automation</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
      let driver = null;
      const phnoInput = document.getElementById('phno');
      const otpInput = document.getElementById('acc_otp');
      const phSubmitBtn = document.getElementById('phsubmitbtn');
      const otpSubmitBtn = document.getElementById('otpsubmitn');
      let sessionId;

      phnoInput.addEventListener('input', () => {
        if (phnoInput.value.length === 10) {
          phSubmitBtn.removeAttribute('disabled');
        } else {
          phSubmitBtn.setAttribute('disabled', true);
        }
      });

      phSubmitBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const phoneNumber = phnoInput.value;

        $.ajax({
          url: 'index.php', // Changed URL to point to the combined file
          method: 'POST',
          data: {
            phno: phoneNumber,
            //driver: serialize(driver)
            //driver: JSON.stringify(driver)
          },
          success: (response) => {
            // Enable OTP input and button
            otpInput.removeAttribute('disabled');
            otpSubmitBtn.removeAttribute('disabled');

            // Store the Selenium session ID
            sessionId = JSON.parse(response).sessionId;
          }
        });
      });

      otpSubmitBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const otp = otpInput.value;
        const phoneNumber = phnoInput.value;
        $.ajax({
          url: 'index.php', // Changed URL to point to the combined file
          method: 'POST',
          data: {
            acc_otp: otp,
            sessionId: sessionId,
            phnos: phoneNumber
          },
          success: (response) => {
              // Display cart value
              const {foodItemsAndQuantities} = JSON.parse(response).foodItemsAndQuantities;
              const cartDataJson = JSON.stringify(foodItemsAndQuantities);
              //fs.writeFileSync('swiggy_cart_data.json', cartDataJson);
              const cartValue = JSON.parse(response).cartValue;
              //console.log(cartValue);
              //document.getElementById('cartValueContainer').innerText = document.getElementById('cartValueContainer').innerText+cartValue;
              // console.log(response);
              // document.getElementById('cartValueContainer').innerText = response;
              runMagicpinPhp();
          }
        });
      });
  });
  function runMagicpinPhp() {
    // Trigger magicpin.php using Ajax
    const phoneNumber = document.getElementById('phno').value;
    const resultbtn = document.getElementById('getresult');
    $.ajax({
      url: 'magicpin.php',
      method: 'POST',
      data: {
        phnos: phoneNumber,
      },
      success: (magicpinResponse) => {
        // Display Magicpin cart value
        //console.log("Magicpin Cart Value: " + magicpinResponse);
        //document.getElementById('magicpinCartValueContainer').innerText = magicpinResponse;
        resultbtn.removeAttribute('disabled');
      }
    });
  }

</script>
<link rel="stylesheet" href="frontend/signin.css">
<link rel="stylesheet" href="frontend/style.css">
</head>
<body>
<nav class="navbar">
            <div class="navbar-left">
                <a href="frontend\index.html" class="navbar-logo h-font">PriceBite</a>
            </div>

            <div class="navbar-right">
                <!-- <a href="./signup.php" class="navbar-button">Sign Up</a> -->
            </div>
        </nav>
        <br>
        <br><br>
    <div class="container">
        <div class="header">
        <h3>Log In</h3>
        <div class="underline-one">    
    </div>
        <br/>

    <div>
        <form class="form-group" id="phoneForm" method="post">
            <h3>Phone Number:</h3><br>
            <input type="tel" id="phno" name="phno" placeholder="Enter your phone number">
            <input type="submit" id="phsubmitbtn" name="phsubmitbtn" value="Submitph">
        </form>
        <form class="form-group2" id="otpForm" disabled>
            <h3>OTP:</h3><br>
            <input type="number" id="acc_otp" name="acc_otp" placeholder="Enter the received OTP" disabled>
            <input type="submit" id="otpsubmitn" name="otpsubmitn" value="Submitotp" disabled>
        </form>
        <form action="frontend/main.php" method="post">
            <input type="submit" id="getresult" name="getresult" value="GET_RESULTS" disabled>
        </form>
        <!-- <div id="cartValueContainer">Cart Value is:</div><br><br>
        <div id="magicpinCartValueContainer">Magicpin Cart Value is:</div> -->

    </div>
</body>

</html>