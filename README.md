# PriceBite

## Introduction
PriceBite is a web-based tool that aims to simplify the process of finding the most cost-effective food delivery platform. With the growing popularity of food delivery services, users often struggle to compare prices across platforms like Swiggy and Magicpin. PriceBite automates this comparison, allowing users to make informed decisions and save money on their food orders.

## Problem Statement
In today’s competitive food delivery market, price fluctuations and discounts vary widely across platforms. Users frequently miss out on savings because manually comparing prices across different platforms is tedious and time-consuming. PriceBite addresses this issue by automatically comparing cart values across platforms, ensuring that users get the best deal available.

## The Solution
PriceBite provides an automated solution that retrieves and compares cart prices from Swiggy and Magicpin. The user starts by entering their cart details on Swiggy. The platform automatically replicates the cart on Magicpin, retrieves the total cost, and compares it with the Swiggy cart value. It then presents the user with the more cost-effective option, simplifying the process of choosing the right platform.

## Implementation Details
PriceBite integrates PHP with the Facebook WebDriver library to automate browser actions via Selenium. The user submits their phone number, and the system interacts with the Swiggy website to retrieve cart details. It uses WebDriver to access Magicpin, replicate the cart, and retrieve the final cart value.

This approach was chosen due to its flexibility in automating browser actions, making it possible to compare cart values dynamically. Error handling, asynchronous operations, and security measures were integrated to ensure a seamless user experience. The modular design also makes it easier to maintain and extend the system in the future.

## Technologies Used
- **Backend**: PHP, Facebook WebDriver (Selenium)
- **Frontend**: HTML, JavaScript, jQuery, Ajax
- **Database**: MySQL
- **Web Automation**: Selenium WebDriver (for Swiggy and Magicpin scraping)
- **Others**: PL/SQL for database triggers and stored procedures.

## Other Details
- The system implements real-time price comparison.
- Future plans include integrating additional platforms, implementing real-time price updates, and adding user personalization features like order history and recommendations.
