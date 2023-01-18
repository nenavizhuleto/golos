<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Система &laquo;Голос&raquo;</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <style>
			body {
			  background-color: #c0c0c0;
			}

			#info-label {
				font-size:0.8em;
			}
    </style>
	</head>
  <body>
		<div class="modal d-flex align-items-center">
		  <div class="modal-dialog shadow">
		    <div class="modal-content border-0">
				  <form id="login-form">
			      <div class="modal-header border-0 px-3 py-2 bg-success text-light">
			        <h6 class="modal-title m-0">Система &laquo;Голос&raquo; &mdash; авторизация</h6>
			      </div>
			      <div class="modal-body pt-4 pb-0">
				      <input id="login" type="text" class="form-control mb-2" placeholder="Логин">
				      <input id="password" type="password" class="form-control" placeholder="Пароль">
						  <label id="info-label" class="text-danger m-0 info-label"></label>
			      </div>
			      <div class="modal-footer px-3 pt-0 pb-3 border-0 d-block text-center">
			        <button id="login-button" type="submit" class="btn btn-sm btn-success" disabled>Вход</button>
			      </div>
				  </form>
		    </div>
		  </div>
		</div>

		<script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <script>
    	function validateInput() {
    		var login = $('#login').val().trim();
    		var password = $('#password').val().trim();

    		var ok = true;
    		if (!login.length)
    			ok = false;

    		if (!password.length)
    			ok = false;

    		if (!ok)
    			$('#login-button').prop('disabled', true);
    		else
    			$('#login-button').prop('disabled', false);
    	}

    	function loginButtonOnClick() {
    		var login = $('#login').val().trim();
    		var password = $('#password').val().trim();

    		var ok = true;
    		if (!login.length)
    			ok = false;

    		if (!password.length)
    			ok = false;

    		if (!ok)
	    		return;

	    	$.post('/users/login/', {
	    		'login': login,
	    		'password': password
	    	}, (data) => {
	    		if (data.result)
		    		window.location.reload();
	    		else
			    	$('#info-label').html('Неверный логин или пароль.');
	    	},'json').fail(() => {
		    	$('#info-label').html('Ошибка соединения.');
	    	});

	    	return;
    	}

    	$('#login').on('input', function() {
    		validateInput();
    	}).focus();

    	$('#password').on('input', function() {
    		validateInput();
    	});

    	$('#login-form').on('submit', function() {
    		loginButtonOnClick();
    		return false;
    	});
    </script>
  </body>
</html>
