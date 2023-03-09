<template>
  <q-page id="addRoute">
    <q-toolbar class="text-white bg-teal-8">
      <q-toolbar-title>
        Add Route
      </q-toolbar-title>
      <q-btn to="/route/saved-routes" flat round dense icon="favorites" color="red" />
    </q-toolbar>

    <q-scroll-area class="q-pa-md form-style">
      <div class="text-h6 q-pb-md">Save your favorite routes!</div>
      <span class="text-body1 q-pd-md">Pick a name for this route</span>

      <q-input
        tabindex="-1"
        v-model="name"
        outlined
        stack-label
        label="Name"
        error-message="Please enter a name for this route"
        :error="errors.name"
      >
        <template v-slot:append>
          <q-icon name="close" @click="name = ''" />
        </template>
      </q-input>

      <div class="text-body1 q-pt-md">Save your favorite routes!</div>

      <q-input
        tabindex="-1"
        readonly
        v-model="from"
        @click="setAddress('From')"
        outlined
        stack-label
        label="From"
        error-message="Please enter a from loaction"
        :error="errors.from"
      >
        <template v-slot:append>
          <q-icon name="close" @click="from = ''" />
        </template>
      </q-input>

      <q-input
        tabindex="-1"
        readonly
        v-model="to"
        @click="setAddress('To')"
        class="q-pt-md"
        outlined
        stack-label
        label="To"
        error-message="Please enter a to loaction"
        :error="errors.to"
      >
        <template v-slot:append>
          <q-icon name="close" @click="to = ''" />
        </template>
      </q-input>

      <RangeSlider v-model:range="range" />

      <div class="q-pt-md">
        <q-btn @click="addRoute" size="lg" class="full-width" color="black" label="Save Route" />
      </div>
    </q-scroll-area>

    <inputAutocomplete
      v-if="show"
      :label="label"
      @address="getAddress($event)"
      @input-visible="show = $event"
    />
  </q-page>
</template>

<script setup>
import { reactive, ref } from 'vue'
import inputAutocomplete from 'src/components/routePage/inputAutocomplete.vue'
import { server } from 'src/boot/axios'
import { useRouteStore } from 'src/stores/route-store'
import { useRouter } from 'vue-router'
import RangeSlider from 'src/components/routePage/RangeSlider.vue'

const router = useRouter()
const routeStore = useRouteStore()

const show = ref(false)
const name = ref('')
const from = ref('')
const to = ref('')
const label = ref('')
const range = ref(1)

const errors = reactive({
  name: null,
  from: null,
  to: null
})

const getAddress = (event) => {
  const selectedLabel = event.label
  if (selectedLabel === 'From') from.value = event.description
  if (selectedLabel === 'To') to.value = event.description
}

const setAddress = (newLabel) => {
  show.value = !show.value
  label.value = newLabel
}

const validation = () => {
  errors.name = name.value === '' ? true : null
  errors.from = from.value === '' ? true : null
  errors.to = to.value === '' ? true : null

  return errors.name || errors.from || errors.to
}

const addRoute = async () => {
  if (validation()) return ''

  try {
    await server.post('/api/routes', {
      user_id: 3,
      name: name.value,
      from: from.value,
      to: to.value,
      range: range.value
    })

    await routeStore.showAllSavedRoutesByUserId(3)

    router.push('/route/saved-routes')
  } catch (error) {
    console.log(error)
  }
}
</script>

<style lang="scss">
#addRoute {
  .form-style {
    margin: 0 auto;
    height: calc(100vh - 180px);
    max-width: 500px;
  }
}
</style>
