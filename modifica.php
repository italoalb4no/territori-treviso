<?php include "inc.php"; ?>

<!DOCTYPE html>
<html>
<head>

	<meta charset='utf-8' />
	<title>Modifica confini territori</title>

	<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.css' rel='stylesheet' />

	<script src='https://api.tiles.mapbox.com/mapbox.js/plugins/turf/v3.0.11/turf.min.js'></script>
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.0.9/mapbox-gl-draw.js'></script>
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.0.9/mapbox-gl-draw.css' type='text/css'/>

	<script src='jquery.min.js'></script>

	<script src='stile-draw.js'></script>

	<style>

		body { margin:0; padding:0; }
		#mappa { position:absolute; top:0; bottom:0; width:100%; }
		
		.info-territorio {
			background: rgba(255, 255, 255, 0.8);
			margin-bottom: -10px;
			padding: 0.4em 0.8em;
			min-width: 40em;
			font-size: 16pt;
			border: 1px solid #b9b9b9;
		}
				
		/* Nasconde logo MapBox */
		.mapboxgl-ctrl-logo {
			opacity: 0 !important;
			display: none !important;
		}

	</style>

</head>
<body>

	<div id="mappa"></div>

	<script>

		var confini = []
		var idTerritorioSelezionato = null


		/**
		 * Token MapBox
		 */
		mapboxgl.accessToken = '<?php echo TOKEN_MAPBOX; ?>'

		/* eslint-disable */
		var map = new mapboxgl.Map({
			container: 'mappa',
			style: 'mapbox://styles/davideblasutto/cjw6rq7fp29671cobin3aex7f',
			center: [12.240393, 45.667163],
			zoom: 16
		});

		// Aggiunge Draw alla mappa
		var draw = new MapboxDraw({
			styles: stiliDraw,
			controls: {
				point: false,
				line_string: false
			}
		})
		map.addControl(draw)

		// Aggiunge pulsante "scarica" alla mappa
		class PulsanteScarica {
			onAdd(map){
				this.map = map;
				this.container = document.createElement('button')
				this.container.className = 'pulsante-scarica mapboxgl-ctrl'
				this.container.textContent = 'Scarica'
				return this.container
			}
			onRemove(){
				this.container.parentNode.removeChild(this.container)
				this.map = undefined
			}
		}
		map.addControl(new PulsanteScarica())


		// Aggiunge pulsante "vai a" alla mappa
		class PulsanteVaiA {
			onAdd(map){
				this.map = map;
				this.container = document.createElement('button')
				this.container.className = 'pulsante-vai-a mapboxgl-ctrl'
				this.container.textContent = 'Vai a'
				return this.container
			}
			onRemove(){
				this.container.parentNode.removeChild(this.container)
				this.map = undefined
			}
		}
		map.addControl(new PulsanteVaiA())
		
		// Aggiunge info territorio
		class InfoTerritorio {
			onAdd(map){
				this.map = map;
				this.container = document.createElement('div')
				this.container.className = 'info-territorio'
				this.container.textContent = ''
				return this.container
			}
			onRemove(){
				this.container.parentNode.removeChild(this.container)
				this.map = undefined
			}
		}
		map.addControl(new InfoTerritorio(), 'bottom-left')
		
		// Aggiunge pulsante "passa a stampa"
		class PulsanteStampa {
			onAdd(map){
				this.map = map;
				this.container = document.createElement('button')
				this.container.className = 'pulsante-stampa mapboxgl-ctrl'
				this.container.textContent = 'Stampa'
				return this.container
			}
			onRemove(){
				this.container.parentNode.removeChild(this.container)
				this.map = undefined
			}
		}
		map.addControl(new PulsanteStampa(), 'top-left')

		/**
		 * Al caricamento della mappa
		 */
		map.on('load', function() {

			// Nasconde il layer "confini-territori" (ci sono già i poligoni gestiti da Draw)
			map.setLayoutProperty('confini-territori', 'visibility', 'none')

		})


		/**
		 * Alla selezione di un territorio
		 */
		map.on('draw.selectionchange', function(featureSelezionate) {

			if (featureSelezionate.features.length == 1) {
				if (featureSelezionate.features[0].id != idTerritorioSelezionato) {
					// Singla feature (territorio) selezionata
					idTerritorioSelezionato = featureSelezionate.features[0].id
					console.log('Selezionato:', idTerritorioSelezionato)
					// Aggiorna info territorio
					var info = featureSelezionate.features[0].properties
					$('.info-territorio').text('Numero: ' + (info.number || 'N/D') + ' - Nome: ' + info.name + ' - Comune: ' +  info.comune)
					  
				}
			}

		})

		/**
		 * Al cambio di modalità
		 */
		map.on('draw.modechange', function(e) {

			if (draw.getMode() == draw.modes.SIMPLE_SELECT) {

				var nuovo

				// Se draw.getSelectedIds() contiene elementi significa che è stato creato un nuovo poligono
				// Usa quello come "territorio selezionato"
				if (draw.getSelectedIds().length > 0) {
					idTerritorioSelezionato = draw.getSelectedIds()[0]
					nuovo = true
				} else {
					nuovo = false
				}

				// Fine modifica territorio
				console.log('Fine modifica territorio:', idTerritorioSelezionato)

				// Estrae la feature
				var featureTerritorio = draw.get(idTerritorioSelezionato)

				// Usa l'ID esistente solo se è "definitivo" (numerico)
				var idAttuale = (("" + idTerritorioSelezionato).length > 10 ? "" : idTerritorioSelezionato)

				var nuovoId = parseInt(prompt('Numero territorio', idAttuale))

				// Se il territorio è nuovo controlla che l'ID non ci sia già
				if (nuovo && typeof draw.get(nuovoId) != "undefined") {
					alert('Il territorio numero ' + nuovoId + ' esiste già.')
					return
				}

				// Chiede ID/numero, nome e comune del territorio appena modificato
				featureTerritorio.id = nuovoId
				featureTerritorio.properties = {
					number: nuovoId,
					name: prompt('Nome territorio', featureTerritorio.properties.name),
					comune: prompt('Comune del territorio', featureTerritorio.properties.comune)
				}
				console.log(featureTerritorio)

				// Ricarica la feature in draw
				draw.delete(idTerritorioSelezionato)
				draw.add(featureTerritorio)

			}

		})

		/**
		 * Al click sul pulsante "Scarica"
		 */
		$(document).on('click', '.pulsante-scarica', function () {

			download('confini-territori.geojson', JSON.stringify(draw.getAll()))

		})

		/**
		 * Al click sul pulsante "Vai a"
		 */
		$(document).on('click', '.pulsante-vai-a', function () {

			var featureTerritorio = draw.get(parseInt(prompt('Vai al territorio')))

			if (featureTerritorio) {
				map.setCenter(featureTerritorio.geometry.coordinates[0][0])
			}

		})

		/**
		 * Al click sul pulsante "Stampa"
		 */
		$(document).on('click', '.pulsante-stampa', function () {

			location.href = 'stampa.php'

		})

		// Ottiene confini attuali da GeoJSON locale
		$.getJSON("confini-territori.geojson?t=" + Date.now(), function(dati) {

			confini = dati
			console.log('Estratti', confini.features.length, 'confini')

			// Li carica in Draw
			draw.add(confini)

		})

		/**
		 * Scarica un testo come file
		 */
		function download(filename, text) {
		  var element = document.createElement('a');
		  element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
		  element.setAttribute('download', filename);

		  element.style.display = 'none';
		  document.body.appendChild(element);

		  element.click();

		  document.body.removeChild(element);
		}

	</script>

	</body>
</html>
