<template>
  <q-page id="loginPage">
    <q-toolbar class="text-white bg-teal-8">
      <q-toolbar-title>
        Login
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
          v-model="email"
          outlined
          stack-label
          label="Email"
          :error-message="errors.email.errorMsg"
          :error="errors.email.errorType"
        >
          <template v-slot:append>
            <q-icon @click="email = ''" name="close" />
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
            <q-icon @click="password = ''" name="close" />
          </template>
        </q-input>

        <div>
          <q-btn @click="login" color="black" size="lg" class="full-width" label="Login"></q-btn>
        </div>

        <div class="q-px-md q-mt-xl text-center">
          <div class="q-mb-md no-account">Don't have an account?</div>
          <q-btn color="teal" outline rounded to="/auth/register" size="15px">Register Here</q-btn>
        </div>
      </div>
    </q-scroll-area>
  </q-page>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useUserStore } from 'src/stores/user-store'
import { useRouter } from 'vue-router'
import { useRouteStore } from 'src/stores/route-store'

const userStore = useUserStore()
const routeStore = useRouteStore()
const router = useRouter()

const email = ref('')
const password = ref('')

const errors = reactive({
  email: { errorMsg: null, errorType: null },
  password: { errorMsg: null, errorType: null }
})

const isValidEmail = (val) => {
  const emailPattern = /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/
  return !emailPattern.test(val)
}

const validation = () => {
  let isError = false

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
  } else {
    errors.password.errorMsg = null
    errors.password.errorType = null
  }

  return isError
}

const login = async () => {
  if (validation()) return

  try {
    // Get the tokens/cookies
    await userStore.getSanctumCookie()

    // Login
    await userStore.login(email.value, password.value)

    // Get the user
    const user = await userStore.fetchUser()

    // Set user data in localstorage (PINIA)
    userStore.setUser(user.data)

    routeStore.showAllSavedRoutesByUserId()

    router.push('/route')
  } catch (error) {
    console.error(error)
  }
}
</script>

<style lang="scss">
#loginPage {
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
