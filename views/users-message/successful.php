


	<div class="svg-container">    
    <h2 style="text-align: center;">Your sms has been sent to users</h2>
	    <svg class="ft-green-tick" xmlns="http://www.w3.org/2000/svg" height="300" width="300" viewBox="0 0 48 48" aria-hidden="true">
	        <circle class="circle" fill="#5bb543" cx="24" cy="24" r="22"/>
	        <path class="tick" fill="none" stroke="#FFF" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M14 27l5.917 4.917L34 17"/>
	    </svg>
        <p style="text-align: center;margin-top: 50px"><a type="button" href="/users-sms/create" class="btn btn-success waves-effect waves-light">Send more sms</a> <span style="font-size:18px" > or </span>  <a href="/users-sms" type="button" class="btn btn-primary waves-effect waves-light">Sms list</a></p>
<br>
	</div>



<style type="text/css">

.tick {
    stroke-dasharray: 29px;
    stroke-dashoffset: 29px;
    animation: draw .5s cubic-bezier(.25, .25, .25, 1) forwards;
    animation-delay: .6s
}

.circle {
    fill-opacity: 0;
    stroke: #219a00;
    stroke-width: 16px;
    transform-origin: center;
    transform: scale(0);
    animation: grow 1s cubic-bezier(.25, .25, .25, 1.25) forwards;   
}

@keyframes grow {
    60% {
        transform: scale(.8);
        stroke-width: 4px;
        fill-opacity: 0;
    }
    100% {
        transform: scale(.9);
        stroke-width: 8px;
        fill-opacity: 1;
        fill: #219a00;
    }
}

@keyframes draw {
    100% { stroke-dashoffset: 0; }
}


body {
    display: table;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}

.svg-container {
    display: block;
    text-align: center;
    vertical-align: middle;
    position: fixed;
  top: 50%;
  left: 50%;
  /* bring your own prefixes */
  transform: translate(-50%, -50%);
}
</style>