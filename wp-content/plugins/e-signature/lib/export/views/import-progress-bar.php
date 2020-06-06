
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
    .esig-import-devbox
    {
        width: 100%;
        margin: 100px auto;
        display:none;
        position: absolute;
        top:25%;
        z-index:999;
    }
    /* Style the background with cool drop shadow effect */
    .esig-import-wrap
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

    .esig-import-desc{
        width:100%;
        position: absolute;
        top:-100px;
        left:-5px;
        text-align: center;
    }
    .esig-import-desc p{
        font-size: 1.50em;
        color: #fff;
    }



</style>


<div id="esig-import-progress-bar" class="esig-import-devbox" style="display:none;">
    <div class="esig-import-desc">
        <p><?php _e("WP E-Signature importing database please do not close or refresh the page.", "esig"); ?></p2>
    </div>  
    <div class="esig-import-wrap">
        <div class="progress-wrap">
            <div class="progress">
                <span class="countup"></span>
            </div>  
        </div>
    </div>
</div>


