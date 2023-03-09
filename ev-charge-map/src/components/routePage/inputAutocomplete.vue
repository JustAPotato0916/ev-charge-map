<template>
  <q-page id="inputAutocomplete">
    <div class="head">
      <q-input :label="label" v-model="input" debounce="500" class="input" outlined autofocus color="white" bg-color="white" label-color="black">
        <template v-slot:append>
          <q-icon @click="emit('input-visible', false)" name="close" />
        </template>
      </q-input>
    </div>
    <div class="addresses">
      <div v-for="(addr, index) in addresses" :key="index">
        <q-list bordered separator>
          <q-item clickable v-ripple @click="getAddress(label, addr.description)">
            <q-item-section>
              <q-item-label>{{ addr.description }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { toRefs, ref, watch } from 'vue'
import { server } from 'src/boot/axios'
import { useUserStore } from 'src/stores/user-store'

const userStore = useUserStore()

const input = ref(null)
const addresses = ref(null)

const props = defineProps({
  label: {
    type: String,
    default: ''
  }
})
const { label } = toRefs(props)

const emit = defineEmits(['address', 'input-visible'])

const getPlaces = async (newValue) => {
  try {
    await userStore.getSanctumCookie()

    const res = await server.post('api/places', { input: newValue })
    addresses.value = res.data.places.predictions
  } catch (error) {
    console.log(error)
  }
}

const getAddress = (label, description) => {
  const addressDetails = {
    label,
    description
  }
  emit('address', addressDetails)
  emit('input-visible', false)
}

watch(() => input.value, getPlaces)
</script>

<style lang="scss" scoped>
#inputAutocomplete {
  position: absolute;
  width: 100%;
  background-color: white;
  top:0;
  left:0;

  .head {
    position: absolute;
    width: 100%;
    background-color: $teal-9;
    height: 95px;
    top:0;
    left:0;
    padding: 20px 15px;
  }

  .input {
    max-width: 500px;
    margin: 0 auto;
  }

  .addresses {
    max-width: 500px;
    margin: 0 auto;
    margin-top: 90px;
    overflow-y: auto;
    font-size: 18px;
    padding: 15px
  }
}
</style>
