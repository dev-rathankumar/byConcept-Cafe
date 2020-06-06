
<style type="text/css">

/* all add-ons install progress bar style */
.page-loader-overlay
{
	        display:none;
	        position:fixed;
	        left:0;
	        top:0;
	        height:100%;
	        width:100%;
	        z-index:15;
	        background: #000;
	        text-align:center;
	        opacity:.8;
}
.esig-addon-devbox
{
  width: 100%;
  margin: 100px auto;
  display:none;
  position: absolute;
  top:5%;
  z-index:999;
}
/* Style the background with cool drop shadow effect */
.esig-addons-wrap
{
  display: block;
  width: 480px;
  margin: 0 auto; 
  position: relative;
  height:70px;
  background: #1a1e22;
  border-radius: 10px;
  box-shadow: inset 0 1px 1px 0 black, 0 1px 1px 0 #36393F;
}
.progress-wrap
{
  display: block;
  width: 465px;
  position: absolute;
  top:0;
  left:0;
  padding:8px;
  border-radius: 8px;
}
/* Style the progress bar and animate */
.progress
{
  display: inline-block;
  margin: 0;   
  padding-top: 18px;
  background: #2e8ffb;
  width: 7%;
  height: 34px;
  border-radius: 8px;
  position: absolute;
  text-align: center;
  background-size: 65px 65px;
  background-image: linear-gradient(135deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent); 
  background-image: -webkit-linear-gradient(135deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);            
  animation: animate-bars 3s linear infinite;
  -webkit-animation: animate-bars 3s linear infinite;
  -moz-animation: animate-bars 3s linear infinite;   
  -ms-animation: animate-bars 3s linear infinite; 
  -o-animation: animate-bars 3s linear infinite; 
}
@-webkit-keyframes animate-bars 
{
  0% { background-position: 0px 0px; }
  100% { background-position: 260px 0px; }
}
@-moz-keyframes animate-bars{
  0% { background-position: 0px 0px;  }
  100% { background-position: 260px 0px;  }
}
@-ms-keyframes animate-bars{
  0% { background-position: 0px 0px;  }
  100% { background-position: 260px 0px;  }
}
@-o-keyframes animate-bars{
  0% { background-position: 0px 0px;  }
  100% { background-position: 260px 0px;  }
}
.countup,.load, .logo{
   text-align: center; 
}
.countup{
  position: relative;
  color: #fff;
}
.load, .logo{
  margin: 0 auto;
}
/* Style the Text at top 'positioned center' */
.load{
 width:100%;
 position: absolute;
 top:-65px;
 left:-5px;
}
.load p{
  font-size: 1.25em;
  color: #fff;
}

</style>

<div class="esig-addon-devbox" style="display:none;">
  <div class="esig-addons-wrap">
    <div class="progress-wrap">
      <div class="progress">
        <span class="countup"></span>
      </div>  
    </div>
  </div>
</div>
<?php 

$plugin_list=get_transient('esign-auto-downloads');
$esig_progress = 1*3000;
	    if($plugin_list)
		{
			 $esig_progress = count($plugin_list)*200;
		}
		
?>
<script type="text/javascript">
(function ($) {
	
	var overlay = $('<div class="page-loader-overlay"></div>').appendTo('body');
        $(overlay).show();

        $(".esig-addon-devbox").show();

        $.fx.interval = 1000;

        $(".progress").animate({ width: "100%" }, {
            duration: <?php echo $esig_progress; ?>,
            step: function (now, fx) {
                if (fx.prop == 'width') {
                    var countup = Math.round((now / 100) * 100) + '%';
                    $(".countup").html(countup);
                }
            },

            start: function () { $(this).before("<div class='load'><p><?php _e('Updating', 'esig'); ?> <a href='https://www.approveme.com/wp-digital-signature-plugin-docs/article/e-signature-wordpress-auto-updates/?progress_bar' target='_blank' style='color:#fff'><?php echo sprintf(__('WP E-signature %s', 'esig'),$plugin->item_name); ?> </a> <?php _e('Add-ons...', 'esig'); ?></p></div>"); },

            complete: function () { $(this).after("<div class='logo'></div>"); },

            done: function () 
			{ 
			$("div.load").html("<p>Successfully Installed Please Wait Redirecting......</p>"); 
			$('.page-loader-overlay').remove();
			$(".esig-addon-devbox").remove();
			}

        });
	
})(jQuery);
</script>

