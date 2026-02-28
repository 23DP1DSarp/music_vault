<script setup lang="ts">
import { onMounted } from 'vue'
import axios from '@/axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

onMounted(async () => {
  try {
    // call backend verify
    await axios.get(`/email/verify/${route.params.id}/${route.params.hash}`)
    alert('Your email is verified!')
    router.push('/login')
  } catch (err) {
    alert('Verification failed.')
  }
})
</script>

<template>
  <div>Verifying…</div>
</template>