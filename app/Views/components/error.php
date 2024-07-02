<style>
    .custom-error-box{
			font-family: Arial, Helvetica, sans-serif;
			font-size: 13px;
		}
		.info, .success, .warning, .error, .validation {
			border: 1px solid;
			margin: 10px 0px;
			padding: 15px 10px 15px 50px;
			background-repeat: no-repeat;
			background-position: 10px center;
		}
		.info {
			color: #00529B;
			background-color: #BDE5F8;
			background-image: url('https://i.imgur.com/ilgqWuX.png');
		}
		.success {
			color: #4F8A10;
			background-color: #DFF2BF;
			background-image: url('https://i.imgur.com/Q9BGTuy.png');
		}
		.warning {
			color: #9F6000;
			background-color: #FEEFB3;
			background-image: url('https://i.imgur.com/Z8q7ww7.png');
		}
		.error{
			color: #D8000C;
			background-color: #FFBABA;
			background-image: url('https://i.imgur.com/GnyDvKN.png');
		}
		.validation{
			color: #D63301;
			background-color: #FFCCBA;
			background-image: url('https://i.imgur.com/GnyDvKN.png');
		}
</style>
<div class = 'row custom-error-box'>
    <div id="customError" class = 'col-xs-12 <?=$message_type;?>'>
        <?=$result['message'];?>
    </div>
</div>