<template>
  <q-dialog id="chargeInfo" v-model="dialogComputed" persistent :maximized="true" transition-show="slide-up"
    transition-hide="slide-down">
    <q-card class="text-white card-bg-color">
      <q-toolbar class="bg-teal-9">
        <q-icon name="ev_station" dense size="40px" color="yellow" />

        <q-toolbar-title>Charger Info</q-toolbar-title>
        <q-space />
        <q-btn flat round dense icon="close" v-close-popup>
          <q-tooltip class="bg-white text-primary">Close</q-tooltip>
        </q-btn>
      </q-toolbar>

      <q-scroll-area class="main">
        <q-list>
          <q-item>
            <q-item-section>
              <q-item-label class="text-h6 q-pl-sm">Charger Location</q-item-label>
              <q-item-label caption class="q-pa-sm">
                <div class="addressSection" v-if="clickedLocation.addressInfo.Title">
                  {{ clickedLocation.addressInfo.Title }}
                </div>
                <div class="addressSection" v-if="clickedLocation.addressInfo.AddressLine1">
                  {{ clickedLocation.addressInfo.AddressLine1 }}
                </div>
                <div class="addressSection" v-if="clickedLocation.addressInfo.AddressLine2">
                  {{ clickedLocation.addressInfo.AddressLine2 }}
                </div>
                <div class="addressSection" v-if="clickedLocation.addressInfo.Town">
                  {{ clickedLocation.addressInfo.Town }}
                </div>
                <div class="addressSection" v-if="clickedLocation.addressInfo.Postcode">
                  {{ clickedLocation.addressInfo.Postcode }}
                </div>
              </q-item-label>

              <div class="row items-center status" v-if="clickedLocation.connections[0].StatusType">
                <q-icon name="power" size="35px" />
                <div :class="{
                  isAvailable: clickedLocation.connections[0].StatusType.IsOperational,
                  isNotAvailable: !clickedLocation.connections[0].StatusType
                    .IsOperational,
                }">
                  {{ clickedLocation.connections[0].StatusType.Title }}
                </div>
              </div>
            </q-item-section>
          </q-item>

          <q-separator spaced inset />

          <q-item>
            <q-item-section>
              <q-item-label class="text-h6 q-pl-sm">Connection Type</q-item-label>
              <q-item-label caption class="q-pa-sm">
                <div class="connectionTypeSection" v-if="clickedLocation.connections[0].ConnectionType.FormalName">
                  Name: {{ clickedLocation.connections[0].ConnectionType.FormalName }}
                </div>
                <div class="connectionTypeSection" v-if="clickedLocation.connections[0].ConnectionType.Title">
                  Title: {{ clickedLocation.connections[0].ConnectionType.Title }}
                </div>
                <div class="connectionTypeSection" v-if="clickedLocation.connections[0].Level.IsFastChargeCapable">
                  Fast charge: <q-icon name="check_circle" color="green-14" size="25px" />
                </div>
                <div class="connectionTypeSection" v-else>
                  Fast charge: <q-icon name="clear" color="red-14" size="25px" />
                </div>
                <div v-if="routeStore.getFrom && routeStore.getTo" class="q-mt-lg">
                  <q-btn v-if="!isMarkerAddressInViaArray" @click="addMarkerAddress" color="primary" class="full-width">Add marker to route</q-btn>
                  <q-btn v-if="isMarkerAddressInViaArray" @click="removeMarkerAddress" color="orange" class="full-width">Remove marker to route</q-btn>
                </div>
              </q-item-label>
            </q-item-section>
          </q-item>

          <q-item>
            <q-item-section v-if="clickedLocation.operatorInfo">
              <q-item-label class="text-h6 q-pl-sm">Company</q-item-label>
              <q-item-label caption class="q-pa-sm" >
                <div class="row items-center companySection" v-if="clickedLocation.operatorInfo.Title">
                  <q-icon name="business" color="white" class="q-mr-sm" size="35px" />
                 {{ clickedLocation.operatorInfo.Title }}
                </div>
                <div class="row items-center companySection" v-if="clickedLocation.operatorInfo.WebsiteURL">
                  <q-icon name="contact_page" color="white" class="q-mr-sm" size="35px" />
                 {{ clickedLocation.operatorInfo.WebsiteURL }}
                </div>
                <div class="row items-center companySection" v-if="clickedLocation.operatorInfo.PhonePrimaryContact">
                  <q-icon name="local_phone" color="white" class="q-mr-sm" size="35px" />
                 {{ clickedLocation.operatorInfo.PhonePrimaryContact }}
                </div>
                <div class="row items-center companySection" v-if="clickedLocation.operatorInfo.ContactEmail">
                  <q-icon name="mail" color="white" class="q-mr-sm" size="35px" />
                 {{ clickedLocation.operatorInfo.ContactEmail }}
                </div>
              </q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { computed, toRefs } from 'vue'
import { useRouteStore } from 'src/stores/route-store'

const routeStore = useRouteStore()

const props = defineProps({
  dialog: {
    type: Boolean,
    default: false
  },
  clickedLocation: {
    type: Object,
    default: null
  },
  via: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:dialog', 'addToViaArray', 'removeFromViaArray'])

const { dialog, clickedLocation, via } = toRefs(props)

const isMarkerAddressInViaArray = computed(() => {
  return via.value.some((item) => item[2] === currentMarkerAddress.value[2])
})

const dialogComputed = computed({
  get: () => dialog.value,
  set: (val) => emit('update:dialog', false)
})

const currentMarkerAddress = computed(() => {
  const lat = clickedLocation.value.addressInfo.Latitude
  const lng = clickedLocation.value.addressInfo.Longitude
  const addrL1 = !clickedLocation.value.addressInfo.AddressLine1 ? '' : clickedLocation.value.addressInfo.AddressLine1
  const addrL2 = !clickedLocation.value.addressInfo.AddressLine2 ? '' : clickedLocation.value.addressInfo.AddressLine2
  const town = !clickedLocation.value.addressInfo.town ? '' : clickedLocation.value.addressInfo.town
  const postcode = !clickedLocation.value.addressInfo.Postcode ? '' : clickedLocation.value.addressInfo.Postcode
  const title = !clickedLocation.value.addressInfo.Country.Title ? '' : clickedLocation.value.addressInfo.Country.Title

  const address = addrL1 + '' + addrL2 + '' + town + '' + postcode + '' + title

  return [lat, lng, address]
})

const addMarkerAddress = () => {
  emit('addToViaArray', currentMarkerAddress.value)
  dialogComputed.value = false
}

const removeMarkerAddress = () => {
  emit('removeFromViaArray', currentMarkerAddress.value)
  dialogComputed.value = false
}
</script>

<style lang="scss" scoped>
#chargeInfo {
  .main {
    height: calc(100vh - 50px);
    margin-top: 30px;
    max-width: 500px;
    margin: 0 auto;
  }

  .card-bg-color {
    background-color: rgba(0, 0, 0, 0.8);
  }

  .addressSection {
    padding-right: 5px;
    font-size: 16px;
    line-height: 25px;
    color: rgb(184, 184, 184);
  }

  .connectionTypeSection {
    padding-right: 5px;
    font-size: 16px;
    line-height: 25px;
    color: rgb(184, 184, 184);
  }
  .companySection{
    padding: 5px 0 5px 5px;
    font-size: 16px;
    line-height: 25px;
    color: rgb(184, 184, 184);
  }

  .status {
    font-size: 16px;
  }

  .isAvailable {
    color: white;
    background-color: $green-14;
    border-radius: 5px;
    padding: 0 10px 0 10px;
  }

  .isNotAvailable {
    color: white;
    background-color: $red-14;
    border-radius: 5px;
    padding: 0 10px 0 10px;
  }
}
</style>
