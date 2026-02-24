<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import ButtonComp from '../../components/Button.vue'
import InputComp from '../../components/input.vue'

const email = ref('')
const password = ref('')
const remember = ref(false)
const errors = ref<Record<string, string[]>>({})

const handleLogin = () => {
  errors.value = {}
  router.post('/login', {
    email: email.value,
    password: password.value,
    remember: remember.value,
  }, {
    onError: (err) => {
      errors.value = err || {}
    }
  })
}
</script>

<template>
  <Head title="Login" />
  <div class="login-box">
    <div class="login-logo">
      <a href="#">Login</a>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <form @submit.prevent="handleLogin">
          <div class="input-group mb-3">
            <InputComp v-model="email" type="email" placeholder="Email" :class="errors.email ? 'is-invalid' : ''" />
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
          </div>
          <div v-if="errors.email" class="invalid-feedback d-block">
            {{ errors.email }}
          </div>
          <div class="input-group mb-3">
            <InputComp v-model="password" type="password" placeholder="Password" :class="errors.password ? 'is-invalid' : ''" />
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
          </div>
          <div v-if="errors.password" class="invalid-feedback d-block">
            {{ errors.password }}
          </div>
          <div class="row">
            <div class="col-4">
              <ButtonComp text="Sign In" type="submit" class="btn btn-primary w-100" />
            </div>
          </div>
        </form>
        <p class="mb-1">
          <!-- <a href="#">I forgot my password</a> -->
        </p>
        <p class="mb-0">
          <Link :href="`/register`" class="text-center">Register a new membership</Link>
        </p>
      </div>
    </div>
  </div>
</template>