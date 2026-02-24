<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

import ButtonComp from '../../components/Button.vue'
import InputComp from '../../components/input.vue'

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const agreeTerms = ref(false)

const errors = ref<Record<string, string[]>>({})

const handleRegister = () => {
  errors.value = {}
  if (password.value !== passwordConfirmation.value) {
    errors.value.password = ['Passwords do not match']
    return
  }

  router.post('/register', {
    name: name.value,
    email: email.value,
    password: password.value,
    password_confirmation: passwordConfirmation.value,
  }, {
    onError: (err) => {
      errors.value = err || {}
    }
  })
}
</script>

<template>
  <Head title="Register" />
  <div class="register-box">
    <div class="register-logo">
      <a href="#"><b>Registration</b></a>
    </div>
    <div class="card">
      <div class="card-body register-card-body">
        <p class="login-box-msg">Register a new membership</p>
        <form @submit.prevent="handleRegister">
          <div class="input-group mb-3">
            <InputComp v-model="name" placeholder="Full name" :class="errors.name ? 'is-invalid' : ''" />
            <span class="input-group-text"><i class="fas fa-user"></i></span>
          </div>
          <div v-if="errors.name" class="invalid-feedback d-block">
            {{ errors.name }}
          </div>
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
          <div class="input-group mb-3">
            <InputComp v-model="passwordConfirmation" type="password" placeholder="Retype password" :class="errors.password_confirmation ? 'is-invalid' : ''" />
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
          </div>
          <div v-if="errors.password_confirmation" class="invalid-feedback d-block">
            {{ errors.password_confirmation }}
          </div>
          <div class="row">
            <div class="col-5">
              <ButtonComp text="Register" type="submit" class="btn btn-primary w-100" />
            </div>
          </div>
        </form>
        <a href="/" class="text-center">I already have a membership</a>
      </div>
    </div>
  </div>
</template>