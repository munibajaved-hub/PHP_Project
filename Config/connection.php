<?php

   $conn =  mysqli_connect("localhost","root","","Ecommerce");
   if(!$conn){
      die("connection failed".mysqli_connect_error());
   }
   // else{
   //    echo "Connected successfully";
   // }
?>