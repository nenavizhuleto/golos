<h5>Активные устройства</h5>
<div id="infoStatus"></div>

<script type="text/javascript">
	var ws;

	function constructWS() {
		var result = new WebSocket("ws://172.17.50.28:58089/", "GOLOS_PROTOCOL");

		result.onerror = function (event) {
		}

		result.onopen = function (event) {
			$('#infoStatus').html('соединение с сервером установлено').css('color', 'green');
		}

		result.onclose = function (event) {
			$('#infoStatus').html('нет соединения с сервером').css('color', 'red');

			setTimeout(function(){
				connect();
			}, 5000);
		}

		result.onmessage = function (event) {
			console.log(event);
			/*
			var msg = JSON.parse(event.data);

			switch (msg.state) {
				case 'update_calls':
					var active_calls = 0;
					msg.calls.forEach(function (call) {
						if (!call.hasOwnProperty('end_datetime')) {
							active_calls ++;
						}
					});

					$('#calls_status').html('соединение с сервером установлено, подключений: ' + msg.clients + ', звонков: ' + active_calls).css('color', 'green');

					break;

				default:
					break;
			}
			*/
		}

		return result;
	}

	function connect() {
		ws = constructWS();
	}

	//connect();
</script>