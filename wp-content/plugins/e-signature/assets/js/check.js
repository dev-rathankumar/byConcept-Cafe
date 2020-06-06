jQuery(document).ready(function($){
	$('#invite_code_error').fadeOut(8000);
	$('#success').fadeOut(8000);
	$('.sigPad').signaturePad();
	$(".btn").click(function() {
		var fullname = ('#fullname').val();
		var emailaddress = ('#emailaddress').val();
		var invitecode = ('#invitecode').val();
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		
		if(fullname == '')
		{
			('#fullnameerror').text('Enter your fullname');
			return false;
		}
        var emailaddressVal = emailaddress;
        if(emailaddressVal == '') {
           ('#emailaddresserror').text('Enter your email address');
			return false;
        }
 
        else if(!emailReg.test(emailaddressVal)) {
            ('#emailaddresserror').text('Enter valid email address');
			return false;
        }
		if(invitecode == '')
		{
			('#invitecodeerror').text('Enter your invitecode');
			return false;
		}
	});
});