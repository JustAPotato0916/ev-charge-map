<template>
  <q-layout>
    <q-header class="bg-teal">
      <q-toolbar>

        <q-btn
          class="desktop-menu"
          flat
          dense
          round
          @click="showDrawer = !showDrawer"
          icon="menu"
        />

        <q-img src="../../app_icon.png" width="40px" height="40px" />
        <q-toolbar-title>Ev Charge Map</q-toolbar-title>
        <q-btn v-if="userStore.getEmail" to="/account" flat round dense icon="account_circle" />
        <q-btn v-else to="/auth" flat round dense icon="login" />

      </q-toolbar>
    </q-header>

    <q-drawer
        v-model="showDrawer"
        show-if-above
        :width="250"
      >
        <q-scroll-area id="menu-section">
          <q-list>

            <q-item clickable v-ripple to="/map">
              <q-item-section avatar><q-icon style="margin-left: 12px" name="explorer" /></q-item-section>
              <q-item-section> Map </q-item-section>
            </q-item>

            <q-item clickable v-ripple to="/route">
              <q-item-section avatar><q-icon name="directions" /></q-item-section>
              <q-item-section> Routes </q-item-section>
            </q-item>

            <q-item clickable v-ripple v-if="!userStore.getEmail" to="/auth">
              <q-item-section avatar><q-icon name="login" /></q-item-section>
              <q-item-section> Auth </q-item-section>
            </q-item>

            <q-item clickable v-ripple v-if="userStore.getEmail" to="/account">
              <q-item-section avatar><q-icon name="account_circle" /></q-item-section>
              <q-item-section> Account </q-item-section>
            </q-item>

            <q-item clickable v-ripple v-if="!pwaIsInstalled" @click="installApp">
              <q-item-section avatar><q-icon name="install_mobile" /></q-item-section>
              <q-item-section> Install PWA </q-item-section>
            </q-item>

          </q-list>
        </q-scroll-area>

        <div class="bg-dark text-white q-pa-md absolute-top">
          <q-item-section>
            <div v-if="userStore.getEmail">
              <q-icon size="20px" name="verified_user" />
              <span class="q-pl-sm">{{ userStore.getFirstName }} {{ userStore.getLastName }}</span>
            </div>
            <div v-else>
              <q-icon size="20px" name="do_not_disturb" />
              <span class="q-pl-sm">No user logged in</span>
            </div>
          </q-item-section>
        </div>
      </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>

    <q-footer elevated>
      <q-tabs class="text-light bg-teal">
        <q-route-tab to="/map" name="/map" icon="explore" label="Map" />
        <q-route-tab to="/route" name="/route" icon="directions" label="Routes" />
        <q-route-tab v-if="userStore.getEmail" to="/account" name="/account" icon="account_circle" label="Account" />
        <q-route-tab v-if="!userStore.getEmail" to="/auth" name="/auth" icon="login" label="Auth" />
        <q-route-tab v-if="!pwaIsInstalled" @click="installApp" name="install" icon="install_mobile" label="Install" />
      </q-tabs>
    </q-footer>
  </q-layout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useUserStore } from 'src/stores/user-store'

const userStore = useUserStore()

const showDrawer = ref(false)
const deferredPrompt = ref(null)

onMounted(async () => {
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    deferredPrompt.value = e
  })
})

const pwaIsInstalled = computed(() => {
  if (window.matchMedia('(display-mode: standalone)').matches) {
    console.log('PWA is installed')
    return true
  }

  console.log('PWA is not installed')
  return false
})

const installApp = async () => {
  deferredPrompt.value.prompt()
  const { outcome } = await deferredPrompt.value.userChoice
  if (outcome === 'dismissed') {
    console.log('PWA is installed')
    return true
  }
}
</script>

<style lang="scss">
#menu-section {
  height: calc(100% - 50px);
  margin-top: 50px;
  border-right: 1px solid;
}
</style>
