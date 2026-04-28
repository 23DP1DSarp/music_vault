<script setup lang="ts">
import { onMounted } from 'vue'
import axios from '@/axios'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

onMounted(async () => {
  try {
    await axios.get(`/email/verify/${route.params.id}/${route.params.hash}`)
    alert('Ваш email подтверждён!')
    router.push('/ru')
  } catch (err) {
    alert('Подтверждение не удалось.')
  }
})
</script>

<template>
  <div>Подтверждение…</div>
</template>
