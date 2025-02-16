<script setup>
import LandingNav from '../Components/LandingNav.vue';
import { usePage } from '@inertiajs/vue3';
// import axios from 'axios'
// import { onMounted, watch } from 'vue';

// const page = usePage()

// const checkSession = async () => {
//     try {
//         const response = await axios.get('/session-check')
//         if (!response.data.authenticated) {
//             router.replace('/login') // Redirect if session is gone
//         }
//     } catch (error) {
//         console.error('Session check failed:', error)
//     }
// }

// // Check session when component mounts
// onMounted(() => {
//     checkSession()
    
//     // Set an interval to check session every 30 seconds
//     setInterval(() => {
//         checkSession()
//     }, 1000) // Adjust as needed (30s = 30000ms)
// })

// // Also recheck when page changes
// watch(() => page.props.auth, () => {
//     checkSession()
// })
</script>
<template>
    <LandingNav />
</template>