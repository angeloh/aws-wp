var $ = jQuery.noConflict();

$(window).load(function(){

	$('#signup.nocf li.first').fadeTo('fast',1);

});

$(document).ready(function(){
	
	
	// EMAIL SIGNUP FIELD WIDTH
	
	emailFieldWidth = $('#signup.nocf li.first').width() - 35 - $('span#submit-button-border').width();
	$('#signup.nocf input[type="text"]').css('width',emailFieldWidth);

	// LIGHTBOX GALLERY (PREMIUM)
	
	$("a[rel^=fancybox]").fancybox({
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'none',
		'titlePosition' 	: 'over',
		'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
			return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + '</span>';
		}
	});
	
	
	// COMMENTS FORM EXPAND
	var mouse_is_inside = false;

    $('#respond').hover(function(){ 
        mouse_is_inside=true; 
    }, function(){ 
        mouse_is_inside=false; 
    });

	$('#respond textarea').focus(function(){
		$(this).css('height','auto');
		$('#commentsform-hidden').fadeIn();
		var commentScroll = $('#respond').offset().top - 15;
		$.scrollTo({top:commentScroll+'px', left:'0px'}, 600);
	});
	
    $('body').mouseup(function(){ 
        if(! mouse_is_inside) {
        	$('#respond textarea').css('height','46px');
        	$('#commentsform-hidden').hide();
        }
    });

	// LAUNCH MODULE TAB
	$('#launchtab a').click(function(){
		$('#launchlitemodule').slideToggle();
		var bubblePos = ((124 - $('a#reusertip').width())/2)*-1;
		$('#reuserbubble').css('right',bubblePos);
		emailFieldWidth = $('#signup.nocf li.first').width() - 35 - $('span#submit-button-border').width();
		$('#signup.nocf input[type="text"]').css('width',emailFieldWidth);
	});
	
	
	// TEAM/PORTFOLIO: SELECT BOX BEHAVIOR
	$('ul.select li').not('.current-menu-item').hide();
		
	$('ul.select').mouseenter(function(){
		$(this).children('li.arrow').removeClass('arrow');
		$(this).children('li').show();
	}).mouseleave(function(){		
		if($(this).children('li').hasClass('current-menu-item')) {
			$(this).children('li.current-menu-item').addClass('arrow');
			$(this).children('li').not('.current-menu-item').hide();
		} else {
			$(this).children('li').not(':first').hide();
		}	
	});
	
	// RETURNING USER TOOLTIP
	
	var bubblePos = ((124 - $('a#reusertip').width())/2)*-1;
	$('#reuserbubble').css('right',bubblePos);
	
	$('a#reusertip').mouseenter(function(){
		$('#reuserbubble').fadeIn('fast');
	}).mouseleave(function(){
		$('#reuserbubble').fadeOut('fast');
	});
	
	$('a#reusertip').click(function(e){
		e.preventDefault();
	});

});

// PRIVACY POLICY MODALS

$().ready(function() {
	$('.jqmWindow#privacy-policy').jqm({trigger: 'a#modal-privacy', overlay:60});
	$('.jqmWindow#privacy-policy').jqmAddClose('a.close'); 
});


// SELECT LINK URL ON CLICK

function SelectAll(id) {
    document.getElementById(id).focus();
    document.getElementById(id).select();
}


// EASING EQUATION

$.extend($.easing,{
    def: 'easeInOutCubic',
    easeInOutCubic: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t + b;
        return c/2*((t-=2)*t*t + 2) + b;
    }
});


// WINDOW LOAD FUNCTIONS

$(window).load(function(){

	// FONTS
	$('h1, h2, h3, label, p').css('visibility','visible');
	
	// FADE IN COUNTDOWN TIMER
	$('#tearoff').animate({opacity:1},300);
	
	// ANIMATE BAR CHART
	var barComplete = $('.barComplete').attr('value');
	$('#bar-complete').animate({
		width:barComplete + '%'
	}, 1800, 'easeInOutCubic', function(){
		$('#bar-complete span').animate({opacity:1},1000);
	});
});


// COUNTDOWN TIMER

$(function () {
	var launchMonth = $('input#launchMonth').attr('value');
	var launchDay = $('input#launchDay').attr('value');
	var launchYear = $('input#launchYear').attr('value');
	var launchDate = new Date();
	launchDate = new Date(launchYear, launchMonth - 1, launchDay, 00, 00, 00);
	$('#tearoff').countdown({
		until: launchDate,
		layout: $('#tearoff').html()		
	});
});


$(document).ready(function(){
	
	// CONTAINER HEIGHT
	var containerHeight = $('#signup-page').height();
	$('#signup-page').css('height',containerHeight);
	
	// MODAL POSITION
	$('.modal-trigger').click(function(){
		var modalPos = $(window).scrollTop() + 70;
		$('.jqmWindow').css('top', modalPos + 'px');
	});
	
	// COUNTDOWN TIMER THREE-DIGITS EXCEPTION
	if($('input.daysLeft').attr('value') > 99) {
		$('#tearoff').addClass('threedigits');
	}

});


// SUBMIT THE FORM

$(function(){

    $("#form").submit(function(e){
 
      	e.preventDefault();
      	
      	$('#submit-button-border').hide();
      	$('#submit-button-loader').fadeIn();
      	      	
        dataString = $("#form").serialize();
        var templateURL = $('#templateURL').attr('value');
        var blogURL = $('#blogURL').attr('value');
        
        $.ajax({
        type: "POST",
        url: templateURL + "/post.php",
        data: dataString,
        dataType: "json",
        success: 
        	function(data) {
        		
	            if(data.email_check == "invalid"){
	               
	                $('#error').html('Invalid Email.').fadeIn();
	                $('#submit-button-loader').hide();
	                $('#submit-button-border').fadeIn();
	                
	            }
	            else if(data.required.length)
	            {
	            	$('.error').hide();
	            	$d = String(data.required).split(",");
					$.each($d, function(k, v){
						$("#" + v + ".error").fadeIn();
					});
	                $('#submit-button-loader').hide();
	                $('#submit-button-border').fadeIn();
	            }
	            else {
	            	
	            	if(data.reuser == "true") {
	            	
		            	$('#form, #error, #presignup-content').hide();
		                $('#returning').fadeIn();
		                
		                var returningCode = blogURL + '/?ref=' + data.returncode;
		                var returningtweetCode = 'http://twitter.com/intent?url=' + encodeURIComponent(returningCode);
		                var tweetMessage = $('input#twitterMessage').attr('value');
		                
		                $('#returning span.user').text(data.email);
		                $('#returning span.clicks').text(data.clicks);
		                $('#returning span.conversions').text(data.conversions);
		                $('#returning input#returningcode').attr('value',returningCode);
		                
	            		$('#tweetblock-return').html('<a href="' + returningtweetCode + '" class="twitter-share-button" data-count="none" data-text="' + tweetMessage + '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>');	

						$('#fblikeblock-return').html('<fb:like id="fbLike" href="'+returningCode+'" send="true" layout="button_count" width="120" show_faces="false" font="arial"></fb:like>');
						FB.XFBML.parse(document.getElementById('fblikeblock-return'));
						
						function renderPlusoneReturning() {
        					gapi.plusone.render('plusoneblock-return', {'href':returningCode, 'size':'tall', 'annotation':'none'});
      					}
      					renderPlusoneReturning();
      					
						var tumblr_link_url_return = returningCode;
					
					    var tumblr_button = document.createElement("a");
					    tumblr_button.setAttribute("href", "http://www.tumblr.com/share/link?url=" + encodeURIComponent(tumblr_link_url_return) + "&name=" + encodeURIComponent(tumblr_link_name) + "&description=" + encodeURIComponent(tumblr_link_description));
					    tumblr_button.setAttribute("title", "Share on Tumblr");
					    tumblr_button.setAttribute("style", "display:inline-block; text-indent:-9999px; overflow:hidden; width:81px; height:20px; background:url('http://platform.tumblr.com/v1/share_1.png') top left no-repeat transparent;");
					    tumblr_button.innerHTML = "Share on Tumblr";
					    document.getElementById("tumblrblock-return").appendChild(tumblr_button);
					    
					    $('#linkinblock-return').html('<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="'+returningCode+'"></script>');
						
	            	
	            	} else {
	            	
		            	$('#form, #error, #presignup-content').hide();
		                $('#success, #success-content').fadeIn();
		                
		                var refermiCode = blogURL + '/?ref=' + data.code;
		                var tweetCode = 'http://twitter.com/intent?url=' + encodeURIComponent(refermiCode);
		                var tweetMessage = $('input#twitterMessage').attr('value');
		                
		                $('#success input#successcode').attr('value',refermiCode);
	            		
	            		$('#tweetblock').html('<a href="' + tweetCode + '" class="twitter-share-button" data-count="none" data-text="' + tweetMessage + '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>');	

						$("#fblikeblock").html('<fb:like id="fbLike" href="'+refermiCode+'" send="true" layout="button_count" width="120" show_faces="false" font="arial"></fb:like>');
						FB.XFBML.parse(document.getElementById('fblikeblock'));
						
						function renderPlusone() {
        					gapi.plusone.render('plusoneblock', {'href':refermiCode, 'size':'tall', 'annotation':'none'});
      					}
      					renderPlusone();
      					
						var tumblr_link_url = refermiCode;
					
					    var tumblr_button = document.createElement("a");
					    tumblr_button.setAttribute("href", "http://www.tumblr.com/share/link?url=" + encodeURIComponent(tumblr_link_url) + "&name=" + encodeURIComponent(tumblr_link_name) + "&description=" + encodeURIComponent(tumblr_link_description));
					    tumblr_button.setAttribute("title", "Share on Tumblr");
					    tumblr_button.setAttribute("style", "display:inline-block; text-indent:-9999px; overflow:hidden; width:81px; height:20px; background:url('http://platform.tumblr.com/v1/share_1.png') top left no-repeat transparent;");
					    tumblr_button.innerHTML = "Share on Tumblr";
					    document.getElementById("tumblrblock").appendChild(tumblr_button);
					    
					    $('#linkinblock').html('<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="'+refermiCode+'"></script>');
					    
					    if(data.pass_thru_error == "blocked"){
	                		$('#pass_thru_error').fadeIn();
	                		$('#pass_thru_error').html('AWeber Sync Error: Email Blocked.');
	            		} 
	          				            		
	            	}
	                    
	            }	
	        }

        });          

    });
});
