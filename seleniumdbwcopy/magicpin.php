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
use \Facebook\WebDriver\WebDriverKeys;
// Function to extract restaurant name from JSON data
function getRestaurantName($jsonData) {
  return $jsonData['restaurantName'];
}
function getRestaurantLocation($jsonData) {
  return $jsonData['restaurantLocation'];
}

// Function to extract food items and quantities from JSON data
function getFoodItemsAndQuantities($jsonData) {
  return $jsonData['foodItemsAndQuantities'];
}

// Function to automate Magicpin cart process
function automateMagicpinCart($restaurantName,$restaurantLocation, $foodItemsAndQuantities,$db) {
  $host = 'http://localhost:4444/wd/hub'; // Selenium server URL
  $capabilities = \Facebook\WebDriver\Remote\DesiredCapabilities::chrome();

  // Set additional browser options
  $options = new \Facebook\WebDriver\Chrome\ChromeOptions();
  $options->addArguments(['--incognito', '--start-maximized']);

  // Set the executable path of Chrome
  $options->setBinary('C:/Program Files/Google/Chrome/Application/chrome.exe');

  $capabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);

  // Start the WebDriver session
  $driver = RemoteWebDriver::create($host, $capabilities);

  // Go to magicpin.com
  $driver->get('https://magicpin.com/');
  $wait = new WebDriverWait($driver, 10);
  // Enter the location in the search bar
  $locationInput = $driver->findElement(WebDriverBy::cssSelector('#city-popup-builder > form > div.searchLocation > input'));
  $locationInput->sendKeys($restaurantLocation);
  sleep(2);
  $locationSelector = $wait->until(
    WebDriverExpectedCondition::presenceOfElementLocated(
        WebDriverBy::cssSelector('#city-popup-builder > form > div.searchLocation > section > div > li:nth-child(1)')
  ));
  
  $locationSelector->click();
  $restaurantInput = $driver->findElement(WebDriverBy::cssSelector('#city-popup-builder > form > div.searchQuery > input'));
  $restaurantInput->sendKeys($restaurantName);
  $restaurantInput->sendKeys(WebDriverKeys::ENTER);
  
  $resturantSelector = $wait->until(
    WebDriverExpectedCondition::presenceOfElementLocated(
        WebDriverBy::cssSelector('#react-search-results > main > section > div.result-card-container > div:nth-child(1) > div.mx-info-holder > div.mx-info > h3 > a')
  ));
  if ($resturantSelector) {
    $resturantSelector->click();
  }
  else{
    $resturantSelector = $wait->until(
      WebDriverExpectedCondition::presenceOfElementLocated(
          WebDriverBy::cssSelector('#react-search-results > main > section > div > div > div.mx-info-holder > div.mx-info > h3 > a')
      ));
    $resturantSelector->click();
  }

  $deliveryTab = $wait->until(
    WebDriverExpectedCondition::presenceOfElementLocated(
        WebDriverBy::cssSelector('#nav-holder > nav > a.delivery-tab.rel-handled')
  ));
  $deliveryTab->click();
  sleep(8);
  foreach ($foodItemsAndQuantities as $foodItem => $quantity) {
    $foodItemInput = $driver->findElement(WebDriverBy::cssSelector('#searchBarCatalog > div > input'));
    $foodItemInput->sendKeys($foodItem);
     
    sleep(3);
    
    $addButton = $driver->findElement(WebDriverBy::cssSelector('div > section > div.itemCountHolder > div > button'));
    $addButton->click();

    
    $foodItemElement = $wait->until(
      WebDriverExpectedCondition::presenceOfElementLocated(
        WebDriverBy::cssSelector('div > section > div.itemCountHolder > div > div > span')
      )
    );


    $quantityText = $foodItemElement->getText();
    $quantityText = (int)$quantityText;
    $newQuantityText='';
    while ($quantityText < (int)$quantity) {
      $plusButton = $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::cssSelector('div > section > div.itemCountHolder > div > div > img.countActions.add.err-handled')
      ));
      //$plusButton = $driver->findElement(WebDriverBy::cssSelector());
      $plusButton->click();
    
      $newQuantityText = $foodItemElement->getText();
      $newQuantityText = (int)$quantityText;
      sleep(2);
      if ($quantityText === $newQuantityText) {
        break; // Quantity not updated, break out of the loop
      }
    
      $quantityText = $newQuantityText;
    }
    $foodItemInput->clear();
  }

  // Retrieve the cart value
  $cartValue = $driver->findElement(WebDriverBy::cssSelector('#merchant-order-summary-react > div > div > div.orderOptions > div > button > div.amountBlock > span:nth-child(1) > span.finalPrice'))->getText();
  
  $phnoss = $_POST["phnos"];
  $ordersResult = $db->query("SELECT Sid FROM Cart WHERE Sid IN (SELECT Sid FROM User WHERE Phoneno='$phnoss') AND orderid IN (SELECT MAX(orderid) FROM Cart WHERE Sid IN (SELECT Sid FROM User WHERE Phoneno='$phnoss'))");

  $ordersRow = $ordersResult->fetch_assoc();
    
  $orders = $ordersRow['Sid'];
  $orderidret=$db->query("Select orderid from cart where Sid='$orders' order by orderid desc limit 1;");
  $orderidresult = $orderidret->fetch_assoc();
  $forderid=$orderidresult['orderid'];
  $cartValueFloat = (float)(str_replace('₹', '', $cartValue));

  $db->query("UPDATE Cart SET mval='$cartValueFloat' WHERE sid='$orders' and orderid='$forderid';");

  return $cartValue;
}

// Read JSON data from file
$jsonData = file_get_contents('swiggy_cart_data.json');
$jsonData = json_decode($jsonData, true);

// Extract restaurant name
$restaurantName = getRestaurantName($jsonData);
$restaurantLocation = getRestaurantLocation($jsonData);

// Extract food items and quantities
$foodItemsAndQuantities = getFoodItemsAndQuantities($jsonData);

// Automate Magicpin cart process and get cart value
$magicpinCartValue = automateMagicpinCart($restaurantName, $restaurantLocation, $foodItemsAndQuantities,$db);
echo json_encode([
  "cartValue" => $magicpinCartValue
]);
exit;
?>