<template>
  <q-page id="myDetails">
    <q-toolbar class="text-white bg-teal-8">
      <q-toolbar-title>
        My Details
      </q-toolbar-title>
    </q-toolbar>

    <q-scroll-area class="form-style">

      <div class="q-pa-md q-gutter-md">
        <div class="text-h6">Update your details!</div>

        <q-input
          v-model="firstName"
          outlined
          stack-label
          label="First Name"
          :error-message="errors.firstName.errorMsg"
          :error="errors.firstName.errorType"
        >
          <template v-slot:append>
            <q-icon @click="firstName = ''" name="close" />
          </template>
        </q-input>

        <q-input
          v-model="lastName"
          outlined
          stack-label
          label="Last Name"
          :error-message="errors.lastName.errorMsg"
          :error="errors.lastName.errorType"
        >
          <template v-slot:append>
            <q-icon @click="lastName = ''" name="close" />
          </template>
        </q-input>

        <div class="text-yellow-10 text-bold q-mt-md">NOTE: This email is read only</div>

        <q-input v-model="userStore.getEmail" outlined stack-label readonly label="Email" />

        <div>
          <q-btn
            @click="updateDetails"
            :disable="!isChangedComputed"
            color="black"
            size="lg"
            class="full-width"
            label="Update Details"
           ></q-btn>
        </div>
      </div>
    </q-scroll-area>
  </q-page>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useUserStore } from 'src/stores/user-store'
import { computed, onMounted, reactive, ref, watch } from 'vue'

const userStore = useUserStore()
const router = useRouter()

const firstName = ref('')
const lastName = ref('')
const isFirstNameSame = ref(false)
const isLastNameSame = ref(false)

const errors = reactive({
  firstName: { errorMsg: null, errorType: null },
  lastName: { errorMsg: null, errorType: null }
})

onMounted(() => {
  firstName.value = userStore.getFirstName || ''
  lastName.value = userStore.getLastName || ''
})

const validation = () => {
  let isError = false

  if (firstName.value.length < 1) {
    errors.firstName.errorMsg = 'Please enter your first name'
    errors.firstName.errorType = true
    isError = true
  } else {
    errors.firstName.errorMsg = null
    errors.firstName.errorType = null
  }

  if (lastName.value.length < 1) {
    errors.lastName.errorMsg = 'Please enter your last name'
    errors.lastName.errorType = true
    isError = true
  } else {
    errors.lastName.errorMsg = null
    errors.lastName.errorType = null
  }

  return isError
}

const isChangedComputed = computed(() => {
  return isFirstNameSame.value || isLastNameSame.value
})

watch(firstName, (val) => {
  if (val !== userStore.getFirstName) {
    isFirstNameSame.value = true
    return ''
  }

  isFirstNameSame.value = false
})

watch(lastName, (val) => {
  if (val !== userStore.getLastName) {
    isLastNameSame.value = true
    return ''
  }

  isLastNameSame.value = false
})

const updateDetails = async () => {
  if (validation()) return ''

  try {
    await userStore.updateUser(firstName.value, lastName.value)

    userStore.setUser({
      first_name: firstName.value,
      last_name: lastName.value
    })

    router.push('/account')
  } catch (error) {
    console.log(error)
  }
}
</script>

<style lang="scss">
#myDetails {
  .form-style {
    margin: 0 auto;
    height: calc(100vh - 180px);
    max-width: 500px;
  }

  .no-account {
    font-size: 17px;
  }
}
</style>
