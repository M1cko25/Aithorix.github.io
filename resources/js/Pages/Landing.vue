<script setup>
import { ref } from "vue";
import Header from "@/Components/LandingComponents/Header.vue";
import HeroSection from "@/Components/LandingComponents/HeroSection.vue";
import FeatureSection from "@/Components/LandingComponents/FeatureSection.vue";
import Testimonials from "@/Components/LandingComponents/Testimonials.vue";
import TemplatesSection from "@/Components/LandingComponents/TemplatesSection.vue";
import LIRASection from "@/Components/LandingComponents/LIRASection.vue";
import AboutSection from "@/Components/LandingComponents/AboutSection.vue";
import ContactSection from "@/Components/LandingComponents/ContactSection.vue";
import CTASection from "@/Components/LandingComponents/CTASection.vue";
import Footer from "@/Components/LandingComponents/Footer.vue";
import StateDisplay from "@/Components/StateDisplay.vue";

const isStateDisplayVisible = ref(false);
</script>

<template>
    <Head title="Landing"/>
    <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100">
        <Header />
        <HeroSection />
        <FeatureSection />
        <TemplatesSection />
        <LIRASection />
        <Testimonials />
        <AboutSection />
        <ContactSection @submit="(value) => {isStateDisplayVisible = value}" />
        <CTASection />
        <Footer />
        <StateDisplay
            v-if="isStateDisplayVisible"
            :pop="isStateDisplayVisible"
            state="success"
            message="Thank you for your message! We will get back to you soon."
            @close="isStateDisplayVisible = false"
        />
    </div>
</template>

<style>
.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-slow {
    animation: float 8s ease-in-out infinite;
}

@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
    100% {
        transform: translateY(0px);
    }
}
</style>
