<template>
  <q-page id="savedRoutes">
    <q-toolbar class="text-white bg-teal-8">
      <q-toolbar-title>
        Saved Route
      </q-toolbar-title>
      <q-btn @click="isLoggedInAddRoute" flat round dense icon="add" size="20px" />
    </q-toolbar>

    <div class="section-title">
      <div class="text-h6 q-mx-sm">Select one of your save routes!</div>
    </div>

    <div v-if="!routeStore.savedRoutes || routeStore.savedRoutes.length < 1" class="text-center q-pt-lg">
      <q-btn @click="isLoggedInAddRoute" flat round dense icon="add_circle_outline" size="33px" />
      <div class="text-h6">Add some routes!</div>
      <div class="q-pa-md info">This is where you can add your most favorite or common journeys!</div>
    </div>

    <q-scroll-area v-else class="list">
      <q-list v-for="route in routeStore.savedRoutes" :key="route.name">
        <q-item>
          <q-item-section top avatar>
            <q-avatar color="green" text-color="white" icon="directions" />
          </q-item-section>

          <q-item-section>
            <q-item-label>{{ route.name }}</q-item-label>
            <q-item-label caption>
              <div class="q-px-sm"><b>From</b>: {{ route.from }}</div>
              <div class="q-px-sm"><b>From</b>: {{ route.to }}</div>
              <div class="q-px-sm"><b>Search Radius</b>: {{ route.range }}</div>
            </q-item-label>
          </q-item-section>

          <q-item-section side bottom>
            <q-btn @click="showSavedRouteOnMap(route.from, route.to, route.range)" class="q-ma-sm" label="Go to map" color="green" size="sm" />
            <q-btn @click="deleteSavedRouteOnMap(route)" class="q-mx-sm" label="Delete" color="red" size="sm" />
          </q-item-section>
        </q-item>
      </q-list>
    </q-scroll-area>
  </q-page>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useRouteStore } from 'src/stores/route-store'
import { server } from 'src/boot/axios'
import { useQuasar } from 'quasar'
import { useUserStore } from 'src/stores/user-store'

const $q = useQuasar()
const routeStore = useRouteStore()
const userStore = useUserStore()
const router = useRouter()

const isLoggedInAddRoute = () => {
  if (!userStore.email) {
    const res = $q.dialog({
      title: 'You\'re not logged in!',
      message: 'Login to add and see your saved routes',
      cancel: true,
      persistent: true
    })

    res.onOk(() => {
      router.push('/auth')
    })

    return
  }

  router.push('/route/add-route')
}

const showSavedRouteOnMap = (from, to, range) => {
  routeStore.setRoute({ from, to, range })
  router.push('/map')
}

const deleteSavedRouteOnMap = async (savedRoute) => {
  try {
    await server.delete(`/api/routes/${savedRoute.id}`)
    await routeStore.showAllSavedRoutesByUserId(3)
  } catch (error) {
    console.log(error)
  }
}
</script>

<style lang="scss">
#savedRoutes {
  .section-title {
    padding: 12px 10px 8px 10px;
    max-width: 500px;
    margin: 0 auto;
  }

  .info {
    font-size: 16px;
  }

  .list{
    margin: 0 auto;
    height: calc(100vh - 250px);
    max-width: 500px;
  }
}
</style>
