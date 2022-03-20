<?php
   include('../conn/db_connection.php');//contains database class
   $conn=new DB_Connection();
    
   /*create country table*/ 
   $employees="CREATE TABLE IF NOT EXISTS EMPLOYEES(";
   $employees.="EMPLOYEE_ID INT AUTO_INCREMENT,";
   $employees.="EMPLOYEE_TYPE VARCHAR(30),";
   $employees.="FIRSTNAME VARCHAR(20),";
   $employees.="LASTNAME VARCHAR(20),";
   $employees.="CONTACT VARCHAR(20),";
   $employees.="USERNAME VARCHAR(20),";
   $employees.="PASSWORD VARCHAR(240),";
   $employees.="PRIMARY KEY(EMPLOYEE_ID)";
   $employees.=");";

   $conn->run_table_query($employees,"EMPLOYEES");


   /*create drivers table*/ 
   $drivers="CREATE TABLE IF NOT EXISTS DRIVERS(";
   $drivers.="DRIVER_ID INT AUTO_INCREMENT,";
   $drivers.="DRIVER_NAME VARCHAR(40),";
   $drivers.="DRIVER_CONTACT VARCHAR(20),";
   $drivers.="PRIMARY KEY(DRIVER_ID)";
   $drivers.=");";

   $conn->run_table_query($drivers,"DRIVERS");

   /*create customers table*/ 
   $customers="CREATE TABLE IF NOT EXISTS CUSTOMERS(";
   $customers.="CUSTOMER_ID INT AUTO_INCREMENT,";
   $customers.="DRIVE_ID INT,";
   $customers.="CUSTOMER_NAME VARCHAR(40),";
   $customers.="CUSTOMER_CONTACT VARCHAR(20),";
   $customers.="PRIMARY KEY(CUSTOMER_ID)";
   $customers.=");";

   $conn->run_table_query($customers,"CUSTOMERS");


   /*create product category table*/ 
   $item_category="CREATE TABLE IF NOT EXISTS PRODUCT_CATEGORY(";
   $item_category.="CATEGORY_ID INT AUTO_INCREMENT,";
   $item_category.="CATEGORY_NAME VARCHAR(60),";
   $item_category.="PRIMARY KEY(CATEGORY_ID)";
   $item_category.=");";

   $conn->run_table_query($item_category,"PRODUCT_CATEGORY");

   /*create expenses table*/ 
   $expenses="CREATE TABLE IF NOT EXISTS EXPENSES(";
   $expenses.="EXPENSES_ID INT AUTO_INCREMENT,";
   $expenses.="ORD_ID INT DEFAULT NULL,";
   $expenses.="EXPENSES_NAME VARCHAR(60),";
   $expenses.="EXPENSES_AMOUNT DECIMAL(20,2),";
   $expenses.="EXPENSES_DATE TIMESTAMP,";
   $expenses.="EXPENSES_YEAR VARCHAR(10),";
   $expenses.="EXPENSES_MONTH VARCHAR(10),";
   $expenses.="EXPENSES_DATE_FIGURE VARCHAR(3),";
   $expenses.="EXPENSES_WEEK_DAY VARCHAR(12),";
   $expenses.="PRIMARY KEY(EXPENSES_ID)";
   $expenses.=");";

   $conn->run_table_query($expenses,"EXPENSES");

   /*create products table*/ 
   $products="CREATE TABLE IF NOT EXISTS PRODUCTS(";
   $products.="PRODUCT_ID INT AUTO_INCREMENT,";
   $products.="CAT_ID INT,";
   $products.="PRODUCT_NAME VARCHAR(50),";
   $products.="PRODUCT_CODE VARCHAR(150),";
   $products.="PRODUCT_TYPE VARCHAR(100),";
   $products.="PRODUCT_PRICE DECIMAL(11,2),";
   $products.="PRIMARY KEY(PRODUCT_ID),";
   $products.="FOREIGN KEY(CAT_ID) REFERENCES PRODUCT_CATEGORY(CATEGORY_ID) ON DELETE CASCADE ON UPDATE CASCADE";
   $products.=");";

   $conn->run_table_query($products,"PRODUCTS");


   /*create orders table*/ 
   $orders="CREATE TABLE IF NOT EXISTS ORDERS(";
   $orders.="ORDER_ID INT AUTO_INCREMENT,";
   $orders.="SELLER_ID INT,";
   $orders.="PRODUCT_ID INT,";
   $orders.="DR_ID INT,";
   $orders.="CA_ID INT,";
   $orders.="CUST_ID INT,";
   $orders.="CUSTOMER_NAME VARCHAR(70),";
   $orders.="CUSTOMER_CONTACT VARCHAR(20),";
   $orders.="ORDERED_QUANTITY INT,";
   $orders.="ORDER_DATE TIMESTAMP,";
   $orders.="ORDER_YEAR VARCHAR(10),";
   $orders.="ORDER_MONTH VARCHAR(10),";
   $orders.="ORDER_DAY_FIGURE VARCHAR(3),";
   $orders.="ORDER_WEEK_DAY VARCHAR(10),";
   $orders.="TOTAL_COST_OF_ORDER DECIMAL(20,2),";
   $orders.="AMOUNT_TO_BE_PAID DECIMAL(20,2),";
   $orders.="AMOUNT_PAID DECIMAL(20,2),";
   $orders.="INVOICE_NUMBER VARCHAR(150),";
   $orders.="REMARKS VARCHAR(150),";
   $orders.="PAYMENT_STATUS INT,";
   $orders.="PRIMARY KEY(ORDER_ID),";
   $orders.="FOREIGN KEY(CA_ID) REFERENCES PRODUCTS(CAT_ID) ON DELETE SET NULL ON UPDATE SET NULL,";
   $orders.="FOREIGN KEY(CUST_ID) REFERENCES CUSTOMERS(CUSTOMER_ID) ON DELETE SET NULL ON UPDATE SET NULL,";
   $orders.="FOREIGN KEY(SELLER_ID) REFERENCES EMPLOYEES(EMPLOYEE_ID) ON DELETE SET NULL ON UPDATE SET NULL";
   $orders.=");";

   $conn->run_table_query($orders,"ORDERS");


   /*create logs table*/ 
   $logs="CREATE TABLE IF NOT EXISTS LOGS(";
   $logs.="LOG_ID INT AUTO_INCREMENT,";
   $logs.="ORD_ID INT,";
   $logs.="PERSON_LOGGING VARCHAR(40),";
   $logs.="LOGGING_INFO TEXT,";
   $logs.="LOGGING_DATE VARCHAR(50),";
   $logs.="LOGGING_YEAR VARCHAR(10),";
   $logs.="LOGGING_MONTH VARCHAR(20),";
   $logs.="LOGGING_DAY_FIGURE VARCHAR(5),";
   $logs.="LOGGING_WEEK_DAY VARCHAR(20),";
   $logs.="PRIMARY KEY(LOG_ID)";
   $logs.=");";

   $conn->run_table_query($logs,"LOGS");



?>