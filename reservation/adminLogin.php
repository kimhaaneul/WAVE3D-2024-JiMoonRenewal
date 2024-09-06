<?php session_start();
include_once("../header2.php");
?>
<!DOCTYPE html>
<html lang="en">
<!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous"> -->
  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>wave3d</title>


    <link href="signin.css" rel="stylesheet">
<style>
  body {
        
/*
        background-image:url('img/back.jpg');
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
*/
      }
	@media (min-width: 992px){
		#login{
			margin-top: -450px;
			margin-left: 83px;
		}
	}
	@media (max-width: 1200px){ 
		#login{
			margin-top: -445px;
    		margin-left: 91px;
		}
	}
  </style>

  </head>

  <body style= background-color:#282f39;>
	  <div style="width: 100%;">
	  <div >
	  <img src="img/back.png" style="width: 720px;">
	  </div>
    <div class="container" id="login">
    
    <?php 
    // echo $_SESSION['id'];

    if (!isset($_SESSION['name'])) { ?>
      <form  action="login-check.php" method="post" encType="multiplart/form-data">
        <!-- <h2 class="form-signin-heading">로그인</h2> -->

          <div class = "row">

            <div class= col-md-4>
              <label for="id" class="sr-only">ID</label>
              <input type="text" id="name" name="name" class="form-control" placeholder="이름" required autofocus>
            </div>
		  <div class= col-md-8></div>
            <div class= col-md-4 style="margin-top: 5px;">
              <label for="pw" class="sr-only">Password</label>
              <input type="text" id="phone" name="phone" class="form-control" placeholder="전화번호" required>
            </div>
            <div class= col-md-2>
            <button class="btn btn-lg btn-primary btn-block" type="submit">예약확인</button>
            </div>
		  	<div class= col-md-12>
             <a href="CMS_register_1.php">예약하기</a>
            </div>
        </div>
    </div>


    <!-- <div class= "container">
    <div class = "row">
            <div class= col-md-5></div> -->
       
      </form>
      </div>
      </div>
      <?php } else {
            $name = $_SESSION['name'];
            echo "<div class = text-left>";
            echo "<p style = color:white>($name)님은 이미 로그인하고 있습니다. ";
            echo "<a>[돌아가기]</a> ";
            echo "<a href=\"adminLogout.php\">[로그아웃]</a></p>";
            echo "</div>";
        } ?>
</div>
     <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
</div>
  </body>
</html>
