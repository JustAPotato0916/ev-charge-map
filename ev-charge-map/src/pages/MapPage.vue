<template>
  <div id="map"></div>

  <q-btn
    v-if="routeStore.getFrom && routeStore.getTo"
    @click="showSteps = true"
    class="show-steps-btn"
    color="black"
    text-color="white"
    label="Show Route"
   />

  <ShowSteps
    v-model:showSteps="showSteps"
    v-model:from="routeStore.getFrom"
    v-model:to="routeStore.getTo"
    v-model:via="via"
  />

  <ChargeInfo
    v-model:clickedLocation="clickedLocation"
    v-model:dialog="dialog"
    v-model:via="via"
    @addToViaArray = "addToViaArray"
    @removeFromViaArray = "removeFromViaArray"
   />
</template>

<script setup>
import { axiosOpenCharge } from '../boot/axios'
import { onMounted, reactive, ref, watch, toRaw } from 'vue'
import { googleObject } from '../boot/google'
import { useRouteStore } from 'src/stores/route-store'
import ChargeInfo from 'src/components/mapPage/ChargeInfo.vue'
import ShowSteps from 'src/components/mapPage/ShowSteps.vue'

const routeStore = useRouteStore()

let map = null
let markers = []
let locations = []
let currentZoomNumber = 0
let boundingBoxString = ''
const latLngs = []
const dialog = ref(false)
const showSteps = ref(false)
const clickedLocation = ref(null)
const via = ref([])

const data = reactive({
  routeResult: null
})

onMounted(async () => {
  await googleObject()

  map = initmap()

  if (data.routeResult) {
    await getChargePointData(data.routeResult)
  }

  window.google.maps.event.addListener(map, 'zoom_changed', async () => {
    currentZoomNumber = map.getZoom()
  })

  window.google.maps.event.addListener(map, 'bounds_changed', async () => {
    const bbox = map.getBounds()
    boundingBoxString = `(${bbox.Va.hi},${bbox.Ja.hi}),(${bbox.Va.lo},${bbox.Ja.lo})`
    if (routeStore.getFrom === null && routeStore.getTo === null) {
      await getChargePointData(null)
    }
  })
})

const initmap = () => {
  const directionsService = new window.google.maps.DirectionsService()
  const directionsRenderer = new window.google.maps.DirectionsRenderer()
  const options = {
    center: { lat: 23.8398, lng: 120.4220 },
    zoom: 8,
    maxZoom: 17,
    fullscreenControl: false,
    zoomControl: false,
    streetViewControl: false,
    mapTypeControl: false
  }
  const map = new window.google.maps.Map(document.getElementById('map'), options)

  if (routeStore.getFrom && routeStore.getTo) {
    getDirections(map, directionsRenderer, directionsService)
  }

  return map
}

const addToViaArray = (address) => {
  setSelectedMarker(address, true)
  via.value.push(address)
}

const removeFromViaArray = (address) => {
  setSelectedMarker(address, false)
  // via.value = via.value.filter((item) => item !== address)
  const array = toRaw(via.value)
  via.value = []

  for (let i = 0; i < array.length; i++) {
    if (array[i][2] !== address[2]) {
      via.value.push(array[i])
    }
  }
}

const getChargePointData = async (newRouteResult) => {
  // if markers array is greater than 0 removeMarkers
  if (markers.length > 0) removeMarkers()

  // Get the lat lng for the polyline
  if (newRouteResult) {
    newRouteResult.routes[0].legs[0].steps.map((step) => {
      latLngs.push('(' + step.lat_lngs[0].lat() + ',' + step.lat_lngs[0].lng() + ')')
      return step
    })
  }
  locations = []

  const response = await axiosOpenCharge.get(
    '?output=json' +
    '&countrycode=TW' +
    `&boundingbox=${currentZoomNumber >= 10 ? boundingBoxString : ''}` +
    `&polyline=${latLngs}` +
    `&distance=${routeStore.getRange}` +
    '&compact=false' +
    '&verbose=false' +
    '&maxresults=100' +
    `&key=${process.env.OPEN_MAPS_API_KEY}`

  response.data.map((location) => {
    locations.push({
      lat: location.AddressInfo.Latitude,
      lng: location.AddressInfo.Longitude,
      addressInfo: location.AddressInfo,
      connections: location.Connections,
      operatorInfo: location.OperatorInfo
    })

    return location
  })

  setMarkers()

  map.setOptions({ minZoom: 8 })
}

const setMarkers = () => {
  locations.map((location) => {
    const marker = new window.google.maps.Marker({
      position: { lat: location.lat, lng: location.lng },
      map
    })

    markers.push(marker)

    marker.addListener('click', () => {
      clickedLocation.value = location
      dialog.value = true
    })

    return location
  })
}

const setSelectedMarker = (address, markerIsSelected) => {
  markers.map((marker) => {
    if (marker.position.lat() === address[0] && marker.position.lng() === address[1]) {
      marker.setMap(null)
    }
    return marker
  })

  let singleLocation = null
  locations.map((location) => {
    if (location.lat === address[0] && location.lng === address[1]) {
      singleLocation = location
    }
    return location
  })

  let image = null
  if (markerIsSelected) image = 'mapIcons/car_charger_icon.png'

  const marker = new window.google.maps.Marker({
    position: { lat: singleLocation.lat, lng: singleLocation.lng },
    map,
    icon: image,
    animation: window.google.maps.Animation.DROP
  })

  markers.push(marker)

  marker.addListener('click', () => {
    clickedLocation.value = singleLocation
    dialog.value = true
  })
}

const removeMarkers = () => {
  locations.map((location, index) => {
    markers[index].setMap(null)

    return location
  })

  markers = []
}

const getDirections = (map, directionsRenderer, directionsService) => {
  directionsRenderer.setMap(map)

  const request = {
    origin: routeStore.getFrom,
    destination: routeStore.getTo,
    optimizeWaypoints: true,
    travelMode: 'DRIVING'
  }

  directionsService.route(request, (result, status) => {
    if (status === 'OK') {
      data.routeResult = result
      directionsRenderer.setDirections(result)
    }
  })
}

watch(() => data.routeResult, async (newRouteResult, oldRouterResult) => {
  if (newRouteResult !== null && newRouteResult !== oldRouterResult) {
    await getChargePointData(newRouteResult)
  }
})

</script>

<style lang="scss" scoped>
#map {
  width: 100%;
  height: 100%;
  position: absolute;
  top:0;
  left:0;
}

.show-steps-btn {
  position: absolute;
  z-index: 1;
  margin: 10px;
}
</style>
