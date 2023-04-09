/**
 * Numero del territrio selezionato/mostrato
 */
var territorioSelezionato

/**
 * L'oggetto "mappa" di MapBox
 */
var map

/**
 * "Copia locale" dei confini, per estrarre metadati
 */
var confini = {}

/**
 * Moltiplicatore attuale (2x o 3x)
 */
var moltiplicatore = 2

/**
 * Stili MapBox per i vari moltiplicatori
 */
var stiliMappa = {
	2: 'mapbox://styles/davideblasutto/cjw6rq7fp29671cobin3aex7f',
	3: 'mapbox://styles/davideblasutto/cjx8n3vce0tv71dok0fy5qhpj'
}

// Ottiene confini attuali da GeoJSON locale
$.getJSON('confini-territori.geojson', function(dati) {

	confini = dati
	console.log('Estratti', confini.features.length, 'confini')

})

/**
 * Carica/ricarica MapBox
 */
function caricaMappa() {

	// Applica classe moltiplicatore
	$('#fronte, #mappa, #controlli, #headerStampa, #footerStampa, #nonVisitare').removeClass('m2x m3x').addClass('m' + moltiplicatore + 'x')

	map = new mapboxgl.Map({
		container: 'mappa',
		style: stiliMappa[moltiplicatore],
		center: [12.240393, 45.667163],
		zoom: 16,
		attributionControl: false,
		pitchWithRotate: false
	})

	// Mostra bussola
	map.addControl(new mapboxgl.NavigationControl({ showZoom: false }))

}

/**
 * Mostra solo i confine di territorioSelezionato
 */
function mostraSoloTerritorioSelezionato() {

	// Filtra il layer
	map.setFilter(
		'confini-territori',
		[ '==', 'number', territorioSelezionato ]
	)

}

/**
 * Al clic sul pulsante "Mostra territorio"
 */
$(document).on('click', '#mostraTerritorio', function() {

	// Chiede quale territorio mostrare
	territorioSelezionato = parseInt(prompt('Numero territorio'))
	console.log('Mostro territorio numero', territorioSelezionato)
	$('#numeroTerritorio').text(territorioSelezionato)

	// Carica nome e comune da copia locale confini
	var confine = confini.features.filter(function(f) { return f.id == territorioSelezionato })[0]
	$('#nomeTerritorio').text(confine.properties.name.trim())
	$('#comuneTerritorio').text(confine.properties.comune.trim())

	// Centra la mappa (circa)
	map.setCenter(confine.geometry.coordinates[0][0])

	// Toglie eventuali nominativi da non visitare rimasti
	$('#nonVisitare tr:not(:first-child):not(:nth-child(2)').remove()
	
	// Chiede nominativi da non visitare
	var nonVisitare = prompt('Nominativi da non visitare (separati da virgola)')
	if (nonVisitare.length) {
		nonVisitare = nonVisitare.split(',')
		nonVisitare.forEach(function(nominativo) {
			console.log(nominativo)
			var riga = $('#templateNonVisitare').clone().detach().removeAttr('id')
			riga.find('td').text(nominativo)
			riga.appendTo('#nonVisitare tbody')
		})
	}

	$('#nonVisitare').css('visibility', nonVisitare.length > 0 ? 'visible' : 'collapse')

	// Mostra solo quello
	mostraSoloTerritorioSelezionato()

})

/**
 * Al clic sul pulsante "Ruota mappa"
 */
$(document).on('click', '#ruotaMappa', function() {

	// Switcha tra 0° e -90°
	if (map.getBearing() == 0) {
		map.rotateTo(-90)
	} else {
		map.rotateTo(0)
	}

})

/**
 * Al clic sul pulsante "Stampa mappa"
 */
$(document).on('click', '#stampaMappa', function() {

	alert('Impostare lo zoom al ' + (100 / moltiplicatore).toFixed(0) + '%')

	window.print()

})


/**
 * Al clic sul pulsante "Cambia moltiplicatore"
 */
$(document).on('click', '#cambiaMoltiplicatore', function() {

	if (moltiplicatore == 2) {
		moltiplicatore = 3
	} else {
		moltiplicatore = 2
	}

	// Ricarica la mappa
	caricaMappa()

})

/**
 * Al clic sul pulsante "+"
 */
$(document).on('click', '#zoomIn', function() {

	map.setZoom(map.getZoom() + 0.03)

})

/**
 * Al clic sul pulsante "-"
 */
$(document).on('click', '#zoomOut', function() {

	map.setZoom(map.getZoom() - 0.03)

})
