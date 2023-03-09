<template>
  <q-page id="registrationPage">
    <q-toolbar class="text-white bg-teal-8">
      <q-toolbar-title>
        Register
      </q-toolbar-title>
    </q-toolbar>

    <q-scroll-area class="form-style">
      <div class="text-h6 text-center q-pt-md">
        <q-img src="/favicon.ico" height="60px" width="60px" />
        <div>Ev Charge Map</div>
      </div>

      <div class="q-pa-md q-gutter-md">
        <div class="text-center">Register and get extra features!</div>

        <q-input
          v-model="firstName"
          outlined
          stack-label
          label="First Name"
          :error-message="errors.firstName.errorMsg"
          :error="errors.firstName.errorType"
        >
          <template v-slot:append>
            <q-icon name="close" />
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
            <q-icon name="close" />
          </template>
        </q-input>

        <q-input
          v-model="email"
          outlined
          stack-label
          label="Email"
          :error-message="errors.email.errorMsg"
          :error="errors.email.errorType"
        >
          <template v-slot:append>
            <q-icon name="close" />
          </template>
        </q-input>

        <q-input
          v-model="password"
          type="password"
          outlined
          stack-label
          label="Password"
          :error-message="errors.password.errorMsg"
          :error="errors.password.errorType"
        >
          <template v-slot:append>
            <q-icon name="close" />
          </template>
        </q-input>

        <q-input
          v-model="confirmPassword"
          type="password"
          outlined
          stack-label
          label="Confirm Password"
          :error-message="errors.confirmPassword.errorMsg"
          :error="errors.confirmPassword.errorType"
        >
          <template v-slot:append>
            <q-icon name="close" />
          </template>
        </q-input>

        <div>
          <q-btn @click="register" color="black" size="lg" class="full-width" label="Register"></q-btn>
        </div>
      </div>
    </q-scroll-area>
  </q-page>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useUserStore } from 'src/stores/user-store'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'

const $q = useQuasar()
const router = useRouter()
const userStore = useUserStore()

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')

const errors = reactive({
  firstName: { errorMsg: null, errorType: null },
  lastName: { errorMsg: null, errorType: null },
  email: { errorMsg: null, errorType: null },
  password: { errorMsg: null, errorType: null },
  confirmPassword: { errorMsg: null, errorType: null }
})

const isValidEmail = (val) => {
  const emailPattern = /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/
  return !emailPattern.test(val)
}

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

  if (email.value.length < 1) {
    errors.email.errorMsg = 'Please enter your email'
    errors.email.errorType = true
    isError = true
  } else if (email.value.length > 0 && isValidEmail(email.value)) {
    errors.email.errorMsg = 'The email you entered is not valid'
    errors.email.errorType = true
  } else {
    errors.email.errorMsg = null
    errors.email.errorType = null
  }

  if (password.value.length < 1) {
    errors.password.errorMsg = 'Please enter your password'
    errors.password.errorType = true
    isError = true
  } else if (password.value.length > 0 && password.value.length < 8) {
    errors.password.errorMsg = 'The minimum password length is 8 characters'
    errors.password.errorType = true
  } else if (password.value !== confirmPassword.value) {
    errors.password.errorMsg = 'The password you entered do not match'
    errors.password.errorType = true
  } else {
    errors.password.errorMsg = null
    errors.password.errorType = null
  }

  return isError
}

const register = async () => {
  if (validation()) return ''

  $q.loading.show({
    message: 'Give us a moment to register you...'
  })

  try {
    // Get the tokens/cookies
    await userStore.getSanctumCookie()

    // Register user
    await userStore.register(
      firstName.value,
      lastName.value,
      email.value,
      password.value,
      confirmPassword.value
    )

    // Get the user
    const user = await userStore.fetchUser()

    // Set user data in localstorage (PINIA)
    userStore.setUser(user.data)

    // Redirect
    router.push('/route')

    $q.notify({
      type: 'positive',
      position: 'top',
      icon: 'check_circle',
      message: `Welcome ${firstName.value}!`
    })

    $q.loading.hide()
  } catch (error) {
    console.error(error)
  }
}
</script>

<style lang="scss">
#registrationPage {
  .form-style {
    margin: 0 auto;
    height: calc(100vh - 180px);
    max-width: 500px;
  }
}
</style>
