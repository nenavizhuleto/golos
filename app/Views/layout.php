<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Система &laquo;Голос&raquo;</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <style>
    	body {
    		padding-top:56px;
    	}

    	#toast-container {
    		margin-top:21px;
	    	z-index:10000;
    	}

    	.link {
    		cursor:pointer;
    	}

    	.link:hover {
    		text-decoration:underline;
    	}
    </style>
		<?php echo view('xu'); ?>
  </head>
  <body>
	  <div id="toast-container" class="fixed-top"></div>
		<nav class="navbar fixed-top navbar-expand navbar-dark bg-dark shadow">
		  <div class="container-fluid">
		    <div class="navbar-brand" href="#">Система &laquo;Голос&raquo;</div>
		    <div class="collapse navbar-collapse" id="navbarSupportedContent">
		    	<div class="me-auto"></div>
			    <?php /* ?>
		      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
		        <li class="nav-item">
		          <a class="nav-link active" aria-current="page" href="#">Home</a>
		        </li>
		        <li class="nav-item">
		          <a class="nav-link" href="#">Link</a>
		        </li>
		        <li class="nav-item dropdown">
		          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
		            Dropdown
		          </a>
		          <ul class="dropdown-menu">
		            <li><a class="dropdown-item" href="#">Action</a></li>
		            <li><a class="dropdown-item" href="#">Another action</a></li>
		            <li><hr class="dropdown-divider"></li>
		            <li><a class="dropdown-item" href="#">Something else here</a></li>
		          </ul>
		        </li>
		        <li class="nav-item">
		          <a class="nav-link disabled">Disabled</a>
		        </li>
		      </ul>
		      <?php */ ?>
		      <div class="navbar-text me-2 link"><?php echo $user->login; ?></div>
		      <button id="topbar-logout" class="btn btn-primary">Выход</button>
		    </div>
		  </div>
		</nav>
		<main class="d-flex flex-nowrap">
		  <div class="d-flex flex-column p-0 text-bg-dark" style="position:fixed;height:100%;width:10em;">
		    <ul class="nav dnav-pills flex-column mb-auto">
		      <li id="sidebar-info" class="nav-link link text-white">Информация</li>
		      <li id="sidebar-devices" class="nav-link link text-white">Устройства</li>
		      <li id="sidebar-rooms" class="nav-link link text-white">Квартиры</li>
		      <li id="sidebar-porches" class="nav-link link text-white">Подъезды</li>
		      <li id="sidebar-buildings" class="nav-link link text-white">Дома</li>
		      <li id="sidebar-blocks" class="nav-link link text-white">Дворы</li>
		      <li id="sidebar-streets" class="nav-link link text-white">Улицы</li>
		      <li id="sidebar-config" class="nav-link link text-white">Конфигурация</li>
		    </ul>
		  </div>

			<div id="content" class="container-fluid p-2" style="margin-left:10em;">
				<div id="page-info" class="d-none"><?php echo view('info'); ?></div>
				<div id="page-devices" class="d-none"><?php echo view('devices'); ?></div>
				<div id="page-rooms" class="d-none"><?php echo view('rooms'); ?></div>
				<div id="page-porches" class="d-none"><?php echo view('porches'); ?></div>
				<div id="page-buildings" class="d-none"><?php echo view('buildings'); ?></div>
				<div id="page-blocks" class="d-none"><?php echo view('blocks'); ?></div>
				<div id="page-streets" class="d-none"><?php echo view('streets'); ?></div>
				<div id="page-config" class="d-none"><?php echo view('config'); ?></div>
			</div>
		</main>

    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>

    <script>
    	function topBarLogout() {
	    	$.post('/users/logout/', {
	    	}, (data) => {
	    		window.location.reload();
	    	},'json');
    	}

    	function sideBarSetItem(item) {
    		$('#sidebar-info').removeClass('bg-primary');
    		$('#sidebar-devices').removeClass('bg-primary');
    		$('#sidebar-rooms').removeClass('bg-primary');
    		$('#sidebar-porches').removeClass('bg-primary');
    		$('#sidebar-buildings').removeClass('bg-primary');
    		$('#sidebar-blocks').removeClass('bg-primary');
    		$('#sidebar-streets').removeClass('bg-primary');
    		$('#sidebar-config').removeClass('bg-primary');
    		$('#sidebar-' + item).addClass('bg-primary');

    		$('#page-info').removeClass('d-block').addClass('d-none');
    		$('#page-devices').removeClass('d-block').addClass('d-none');
    		$('#page-rooms').removeClass('d-block').addClass('d-none');
    		$('#page-porches').removeClass('d-block').addClass('d-none');
    		$('#page-buildings').removeClass('d-block').addClass('d-none');
    		$('#page-blocks').removeClass('d-block').addClass('d-none');
    		$('#page-streets').removeClass('d-block').addClass('d-none');
    		$('#page-config').removeClass('d-block').addClass('d-none');
    		$('#page-' + item).removeClass('d-none').addClass('d-block');

    		switch (item) {
    			case 'info':
	    			break;

    			case 'devices':
						devicesTable.reloadData();
	    			break;

    			case 'rooms':
						roomsTable.reloadData();
	    			break;

    			case 'porches':
						porchesTable.reloadData();
	    			break;

    			case 'buildings':
   					buildingsTable.reloadData();
	    			break;

    			case 'blocks':
    				blocksTable.reloadData();
	    			break;

    			case 'streets':
    				streetsTable.reloadData();
	    			break;

	    		case 'config':
	    			break
    		}
    	}

    	var toastContainer;

			window.addEventListener('DOMContentLoaded', (event) => {
				// Toast
				toastContainer = new XuToastContainer(null, { classes: ['top-0', 'start-50', 'translate-middle-x'] });
				document.getElementById('toast-container').appendChild(toastContainer.element);

	    	// Topbar
	    	$('#topbar-logout').on('click', function() {
	    		topBarLogout();
	  		});

	    	// Sidebar
	    	$('#sidebar-info').on('click', function() {
	    		sideBarSetItem('info');
	  		});

	    	$('#sidebar-devices').on('click', function() {
	    		sideBarSetItem('devices');
	  		});

	    	$('#sidebar-rooms').on('click', function() {
	    		sideBarSetItem('rooms');
	  		});

	    	$('#sidebar-porches').on('click', function() {
	    		sideBarSetItem('porches');
	  		});

	    	$('#sidebar-buildings').on('click', function() {
	    		sideBarSetItem('buildings');
	  		});

	    	$('#sidebar-blocks').on('click', function() {
	    		sideBarSetItem('blocks');
	  		});

	    	$('#sidebar-streets').on('click', function() {
	    		sideBarSetItem('streets');
	  		});

	    	$('#sidebar-config').on('click', function() {
	    		sideBarSetItem('config');
	  		});

	   		sideBarSetItem('config');
	   	});
    </script>
  </body>
</html>
