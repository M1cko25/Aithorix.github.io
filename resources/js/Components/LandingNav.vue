<script setup>
import logo from '../../../public/assets/Logo2.png'
import Button from '../Components/Button.vue'

defineProps({

})

const signUp = () => {
}
</script>
<template>
    <header class="w-full py-4 px-6 flex row justify-between items-center">
        <div class="flex row items-center md:gap-4">
            <div>
            <img :src="logo" alt="Aithorix Logo" width="175" height="40">
        </div>
        <nav>
            <ul class="flex row md:gap-8 text-lg transition-all duration-300">
                <li><a href="" class="menu-link">Features</a></li>
                <li><a href="" class="menu-link">Pricing</a></li>
                <li><a href="" class="menu-link">About Us</a></li>
                <li><a href="" class="menu-link">Contact Us</a></li>
            </ul>
        </nav>
        </div>
        <div>
            <Link class="btn-primary px-4 py-2" :href="route('login')">Log In</Link>
        </div>
    </header>
</template>
<style scoped>
  .menu-link {
    position: relative;
  }
.menu-link::before {
    content: '';
    width: 0;
    height: 2px;
    border-radius: 2px;
    background-color: #000;
    position: absolute;
    bottom: -.25rem;
    left: 50%;
    transition: width .4s, left .4s;
  }

.menu-link:hover::before {
    width: 100%;
    left: 0;
}
</style>