<?php
/*if(isset($_POST['submit']))
{
	echo"Sachin";
$to='sachin.sachin.thakur807@gmail.com';
$subject = 'Apartment Query/Feedback Received';
$email=$_POST['email'];
$phone = $_POST['phone'];
$name = $_POST['name'];
// The message
$message = $_POST['message'];
$message = $message.' sent by '.$email.' Contact: '.$phone;
$message = wordwrap($message, 70, "\r\n");

// Send
mail($to,$subject, $message);
//mail($to, $subject, $message, $headers);
}*/
?>
<html lang="en">
     <head>
     <title>Contacts</title>
	 <style>
 #map {
   width: 100%;
   height: 400px;
   background-color: grey;
 }
</style>
     <meta charset="utf-8">
     <meta name = "format-detection" content = "telephone=no" />
     <link rel="icon" href="images/favicon.ico">
     <link rel="shortcut icon" href="images/favicon.ico" />
     <link rel="stylesheet" href="css/form.css">
  <link rel="stylesheet" type="text/css" href="css/tooltipster.css" />
     <link rel="stylesheet" href="css/style.css">
     <script src="js/jquery.js"></script>
     <script src="js/jquery-migrate-1.2.1.js"></script>
     <script src="js/script.js"></script> 
     <script src="js/superfish.js"></script>
     <script src="js/TMForm.js"></script>
     <script src="js/jquery.ui.totop.js"></script>
     <script src="js/jquery.equalheights.js"></script>
     <script src="js/jquery.mobilemenu.js"></script>
     <script src="js/jquery.easing.1.3.js"></script>
      <script src="js/jquery.tooltipster.js"></script>
     <script>
	 
	 function onSubmitClick()
	 {
		 //alert("message");
		 $.ajax({
			 type: "POST",
			 url:"mailAjaxResponse.php",
			 data: $("#form").serialize(),
			 success:function(result)
			 {
				 alert("Message sent");
			 }
		 });
	 }
	 
       $(document).ready(function(){
        $().UItoTop({ easingType: 'easeOutQuart' });
        $('.tooltip').tooltipster();
        });
     </script>

    <!--[if lt IE 9]>
       <div style=' clear: both; text-align:center; position: relative;'>
         <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
           <img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
         </a>
      </div>
      <script src="js/html5shiv.js"></script>
      <link rel="stylesheet" media="screen" href="css/ie.css">


    <![endif]-->
     </head>
     
     <body class="" id="top">
     
<!--==============================header=================================--> 
<header>  
  <div class="container_12">
    <div class="grid_12">
        <h1>
        <a href="index.html">
          <img src="images/logo.png" alt="Your Happy Family">
        </a>
      </h1>
        <div class="menu_block ">
          <nav class="horizontal-nav full-width horizontalNav-notprocessed">
            <ul class="sf-menu">
                 <li><a href="index.html">Home</a></li>
                <!-- <li><a href="index-1.html">Services</a></li>-->
                 <li><a href="index-2.html">Gallery</a></li>
                 <li><a href="index-3.html">Blog</a></li>
                 <li class="current"><a href="index-4.html">Contacts</a></li>
               </ul>
            </nav>
           <div class="clear"></div>
        </div>
      </div>      
   </div>
</header>
<!--==============================Content=================================-->
  <div class="container_12">
    <div class="grid_12">
      <h2>Contacts</h2>
            <div id="map">
			
			
		
			<script>
      function initMap() {
        var uluru = {lat: 31.325640, lng: 75.573329};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
      }
    </script>
	</div>
	<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAp8H8Bz0dFvwppSYK5wLIbwCHtSz9i5ws&callback=initMap">
    </script>
			
			
			
			
			
            <!--<figure class=" ">
                          <iframe src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=Brooklyn,+New+York,+NY,+United+States&amp;aq=0&amp;sll=37.0625,-95.677068&amp;sspn=61.282355,146.513672&amp;ie=UTF8&amp;hq=&amp;hnear=Brooklyn,+Kings,+New+York&amp;ll=40.649974,-73.950005&amp;spn=0.01628,0.025663&amp;z=14&amp;iwloc=A&amp;output=embed"></iframe>
               </figure>-->
              
          </div>
    </div>  
    <div class="clear"></div>
    <div class="grid_3">
	<br>
	<br>
      <h3 class="head1">Find Us</h3>
      <p>You can always get 24/7 support for all <span class="col1"><a href="http://www.templatemonster.com/website-templates.php" rel="nofollow">premium website templates</a></span> from <br>TemplateMonster. Free themes go without it. </p>
      <p><span class="col1"><a href="http://www.templatetuning.com/" rel="nofollow">TemplateTuning</a></span> will help you with any questions regarding customization of the chosen themes.</p>
      <p>The Company Name Inc. <br>
      8901 Marmora Road, <br>
      Glasgow, D04 89GR.</p>
      Freephone:+1 800 559 6580 <br>
      Telephone:+1 800 603 6035<br>
      FAX:+1 800 889 9898<br>
      E-mail: <a href="#" class="col1">mail@demolink.org</a>
    </div>
	
    <div class="grid_9">
      <h3 class="head1">Contact Form</h3>
            <form id="form" method="POST">
				<div class="success_wrapper">
					<div class="success-message">Contact form submitted</div>
				</div>
				
				<label class="name">
					<input type="text" placeholder="Name:" data-constraints="@Required @JustLetters" name="name"/>
					
					<span class="empty-message">*This field is required.</span>
					<span class="error-message">*This is not a valid name.</span>	
				</label>
				
				<label class="email">
					<input type="text" placeholder="E-mail:" data-constraints="@Required @Email" name="email"/><span class="empty-message">*This field is required.</span>
					<span class="error-message">*This is not a valid email.</span>
				</label>
				
				<label class="phone">
					<input type="text" placeholder="Phone:" data-constraints="@Required @JustNumbers" name="phone"/>
					<span class="empty-message">*This field is required.</span>
					<span class="error-message">*This is not a valid phone.</span>
				</label>
				
				<label class="message">
					<textarea placeholder="Message:" data-constraints='@Required @Length(min=20,max=999999)' name="message"></textarea>
					<span class="empty-message">*This field is required.</span>
					<span class="error-message">*The message is too short.</span>
				</label>
				
				<label class="message" style="align: center">
					<button onclick="onSubmitClick()">Submit</button>
					<!--<input type="submit" name="submit" value="Submit" />-->
				</label>
			</form>
	</div>
<!--==============================footer=================================-->
<footer>   
    <div class="container_12">
      
      <div class="grid_12">
        <div class="socials">
      <section id="facebook">
        <a href="#" target="_blank"><span id="fackbook" class="tooltip" title="Link us on Facebook">f</span></a>
        </section>
        <section id="twitter">
        <a href="#" target="_blank"><span id="twitter-default" class="tooltip" title="Follow us on Twitter">t</span></a>
        </section>      
        <section id="google">
        <a href="#" target="_blank"><span id="google-default" class="tooltip" title="Follow us on Google Plus">g<span>+</span></span></a>
        </section>        
        <section id="rss">
        <a href="#" target="_blank"><span id="rss-default" class="tooltip" title="Follow us on Dribble">d</span></a>
        </section>
       
      

        </div>
        <div class="copy">
		YourHome &copy; Since 2014 <br> Website created by Kreative Ideas,Toronto,Canada
        </div>
         
      </div>
    </div>  
</footer>
</body>
</html>
