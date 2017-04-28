<?php
echo '
				</div>
			</div>
		</div>
		
		<!--BOTTOM CONTAINER-->
		<div id="bottom-container" class="container">
			<a style="clear:left;" href="http://forum.diplomatic-war.omarabdelbari.com">Forum</a>
			<a href="http://support.diplomatic-war.omarabdelbari.com">Support</a>
			<a href="http://bugs.diplomatic-war.omarabdelbari.com">Report a Bug</a>
			<a style="clear:right;" href="http://diplomatic-war.omarabdelbari.com/contact-us.php">Contact Us</a>
			
			<br>
			
			COPYRIGHT 2014
		</div>
		
		<img id="image-loader" alt="image-loader" style="display:none;" src="Graphics/banners/banner-test.jpg">
		
	</div>

<script>
//MODIFYING VARIABLES
//Images for banner
var slideImages = [
	"Graphics/banners/banner-test.jpg",
	"Graphics/banners/banner-test.jpg",
	"Graphics/banners/banner-test.jpg",
	"Graphics/banners/banner-test.jpg"
]

/*TOP IMAGE SLIDE*/
//For firefox, load images on hidden element so that its not slow when its auto moving
selectedImage=0;
interval = 2500; //2.5sec for autoslider

$("#image-loader").attr("src", slideImages[0]);
$("#image-loader").attr("src", slideImages[1]);
$("#image-loader").attr("src", slideImages[2]);
$("#image-loader").attr("src", slideImages[3]);

$("#top-slide-image-0").attr("src", slideImages[0]);
$("#top-slide-image-1").attr("src", slideImages[1]);
$("#top-slide-image-2").attr("src", slideImages[2]);
$("#top-slide-image-3").attr("src", slideImages[3]);

$("#top-slide-tab-0").hover(
	function () { clearInterval(slideInterval); changeSlideImage(0); },
	function () { slideInterval = setInterval(function(){resumeAutoSlide()}, interval); }
);
$("#top-slide-tab-1").hover(
	function () { clearInterval(slideInterval); changeSlideImage(1); },
	function () { slideInterval = setInterval(function(){resumeAutoSlide()}, interval); }
);
$("#top-slide-tab-2").hover(
	function () { clearInterval(slideInterval); changeSlideImage(2); },
	function () { slideInterval = setInterval(function(){resumeAutoSlide()}, interval); }
);
$("#top-slide-tab-3").hover(
	function () { clearInterval(slideInterval); changeSlideImage(3); },
	function () { slideInterval = setInterval(function(){resumeAutoSlide()}, interval); }
);

slideInterval = setInterval(function(){resumeAutoSlide()}, interval);

function changeSlideImage(i) { //i is the number of the tab/image to switch to
	$("#top-slide-image").attr("src", slideImages[i]);
	
	$("#top-slide-tab-" + selectedImage).removeClass("top-slide-navbutton-active").addClass("top-slide-navbutton-inactive")
		.attr("src", "Graphics/SlideTabInactive.png");
	
	$("#top-slide-tab-" + i).removeClass("top-slide-navbutton-inactive").addClass("top-slide-navbutton-active")
		.attr("src", "Graphics/SlideTabActive.png");
			
	selectedImage = i;
}

function resumeAutoSlide() {
	switch(selectedImage) {
		case 0: case 1: case 2:
			newSelectedImage = selectedImage + 1;
			break;
		case 3:
			newSelectedImage = 0;
			break;
		default:
			break;
	}
	
	changeSlideImage(newSelectedImage);
	selectedImage = newSelectedImage;
}
	
</script>
	
</BODY>
</HTML>';
?>
