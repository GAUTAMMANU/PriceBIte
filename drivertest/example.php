<?php

require_once('vendor/autoload.php');
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

$web_driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', Facebook\WebDriver\Remote\DesiredCapabilities::chrome());
    $web_driver->get("http://google.com");

    $wait = new WebDriverWait($web_driver, 10); // 10 seconds timeout

// Use WebDriverWait to wait for element visibility
    $element = $wait->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("APjFqb"))
    );
    if($element) {
      $element->sendKeys("TestingBot");
      //$element->submit();
    }
    $element = $wait->until(
      
      WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::className("gNO89b"))

  );
    $element->click();

    //print $web_driver->getTitle();
    $web_driver->quit();
?>