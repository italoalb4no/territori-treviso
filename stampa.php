<?php include "inc.php"; ?>

<!DOCTYPE html>
<html>
<head>

	<meta charset='utf-8' />
	<title>Stampa territori</title>

	<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.css' rel='stylesheet' />

	<script src='jquery.min.js'></script>

	<link href='stile-stampa.css' rel='stylesheet' />

	<script>

		/**
		 * Token MapBox
		 */
		mapboxgl.accessToken = '<?php echo TOKEN_MAPBOX; ?>'

	</script>

	<script src='stampa.js'></script>

</head>
<body>

	<div id="fronte">

		<table id="headerStampa">
			<tr>
				<td id="cellaNumeroTerritorio">
					Участок <span id="numeroTerritorio">888</span>
				</td>
				<td>
					<span id="comuneTerritorio">Treviso</span>
					<span id="nomeTerritorio">AAA</span>
				</td>
				<td class="numero-nominativi"></td>
				<td class="numero-nominativi"></td>
			</tr>
		</table>

		<center>
		<table id="nonVisitare">
			<tr>
				<th>Н. З.</th>
			</tr>
			<tr id="templateNonVisitare">
				<td>Indirizzo</td>
			</tr>
		</table>
		</center>


		<div id="footerStampa">
			<div>
				Храни, пожалуйста, эту карту в чехле. Не пачкай ее, не делай на ней пометок и не сгибай. После того как участок обработан, сообщи об этом брату, ответственному за участки.
			</div>
		</div>

	</div>

	<div id="mappa"></div>

	<div id="controlli">

		<button id="mostraTerritorio">Mostra territorio</button>
		<button id="stampaMappa">Stampa mappa</button>
		<hr>
		<button id="ruotaMappa">Ruota mappa</button>
		<button id="cambiaMoltiplicatore">Cambia moltiplicatore</button>
		<button id="zoomOut">-</button>
		<button id="zoomIn">+</button>
		<hr>
		<button onclick="location.href = 'modifica.php'">Modifica</button>

	</div>

	<script> caricaMappa() </script>

	</body>
</html>
