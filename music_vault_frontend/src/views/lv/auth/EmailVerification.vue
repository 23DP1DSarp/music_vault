<script setup lang="ts">
import { onMounted } from 'vue'
import axios from '@/axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

onMounted(async () => {
  try {
    await axios.get(`/email/verify/${route.params.id}/${route.params.hash}`)
    alert('Jūsu e-pasts ir apstiprināts!')
    router.push('/')
  } catch (err) {
    alert('Apstiprināšana neizdevās.')
  }
})
</script>

<template>
  <div>Apstiprināšana…</div>
</template>
