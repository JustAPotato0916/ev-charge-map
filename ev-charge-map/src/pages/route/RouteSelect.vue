<template>
  <q-page id="routeSelect">
    <q-toolbar class="text-white bg-teal-8">
      <q-toolbar-title>
        Route
      </q-toolbar-title>
      <q-btn to="/route/saved-routes" flat round dense icon="favorites" color="red" />
    </q-toolbar>

    <div class="q-pa-md">
      <div class="text-h6 q-pb-md">Where would you like to go?</div>

      <q-input @click="setAddress('From')"
        tabindex="-1"
        readonly
        v-model="from"
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
        @click="setAddress('To')"
        tabindex="-1"
        readonly
        v-model="to"
        class="q-pt-md"
        outlined
        stack-label
        label="From"
        error-message="Please enter a to loaction"
        :error="errors.to"
      >
        <template v-slot:append>
          <q-icon name="close" @click="to = ''" />
        </template>
      </q-input>

      <RangeSlider v-model:range="range" />

      <div class="q-pt-md">
        <q-btn size="lg" @click="search" class="full-width" color="black" label="Search" />
      </div>
    </div>

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
import { useRouter } from 'vue-router'
import { useRouteStore } from 'src/stores/route-store'
import inputAutocomplete from 'src/components/routePage/inputAutocomplete.vue'
import RangeSlider from 'src/components/routePage/RangeSlider.vue'

const router = useRouter()
const routeStore = useRouteStore()

const show = ref(false)
const label = ref(null)
const from = ref('')
const to = ref('')
const range = ref(1)

const errors = reactive({
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
  errors.from = from.value === '' ? true : null
  errors.to = to.value === '' ? true : null

  return errors.from || errors.to
}

const search = () => {
  if (validation()) return ''
  routeStore.setRoute({
    from: from.value,
    to: to.value,
    range: range.value
  })

  router.push('/map')
}
</script>

<style lang="scss">
#routeSelect {
  .form-style{
    margin: 0 auto;
    height: calc(100vh - 180px);
    max-width: 500px;
  }
}
</style>
