$(document).ready(function() {
	$(".button-collapse").sideNav();
	$('.modal').modal();
	$('select').material_select();
});

function handleAddUser() {

	var first_name 	= 	$("input[name='first']").val(),
		last_name 	= 	$("input[name='last']").val(),
		email		=	$("input[name='email_address']").val(),
		password	=	$("input[name='password']").val(),
		password_r	=	$("input[name='password_r']").val();

	var isError = 0;

	if(first_name.length === 0) {
		$("label[for='first']").text("First name is required").css('color', 'red');
		isError = 1;
	} 

	if(last_name.length === 0) {
		$("label[for='last']").text("Last name is required").css('color', 'red');
		isError = 1;
	} 

	if(email.length === 0) {
		$("label[for='email_address']").text("Email is required").css('color', 'red');
		isError = 1;
	} 

	if (password.length === 0) {
		$("label[for='password']").text("Password is required").css('color', 'red');
		isError = 1;		
	}

	if (password != password_r) {
		$("label[for='password']").text("Passwords don't match").css('color', 'red');
		isError = 1;		
	}

	if (isError === 1) {
		return false;
	}
}

function handleEditUser() {

	var first_name 	= 	$("input[name='first_name']").val(),
		last_name 	= 	$("input[name='last_name']").val(),
		email		=	$("input[name='email']").val(),
		password	=	$("input[name='password']").val(),
		password_r	=	$("input[name='password_r']").val();

	var isError = 0;

	if(first_name.length === 0) {
		$("label[for='first_name']").text("First name is required").css('color', 'red');
		isError = 1;
	} 

	if(last_name.length === 0) {
		$("label[for='last_name']").text("Last name is required").css('color', 'red');
		isError = 1;
	} 

	if(email.length === 0) {
		$("label[for='email']").text("Email is required").css('color', 'red');
		isError = 1;
	} 

	if (password != password_r) {
		$("label[for='password']").text("Passwords don't match").css('color', 'red');
		isError = 1;		
	}

	if (isError === 1) {
		return false;
	}
}