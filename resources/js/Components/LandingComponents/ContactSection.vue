<script setup>
import { MapPin, Mail, Phone, Twitter, Linkedin, Facebook, Instagram } from 'lucide-vue-next';
import { ref } from 'vue';
import {useForm} from '@inertiajs/vue3';
import TextField from '@/Components/TextField.vue';

const emit = defineEmits(['submit']);
const isSubmitting = ref(false);
const form = useForm({
    name: "",
    email: "",
    subject: "",
    message: "",
});

const handleSubmit = ()=>{
    // form.post(route('contact.store'), {
    //     onSuccess: (page) => {
    //         form.reset();
    //         emit('submit', true);
    //     },
    //     onError: (errors) => {
    //         console.error(errors);
    //     },
    //     onFinish: () => {
    //         form.reset();
    //     }
    // });
    emit('submit', true);
    form.reset();
}
</script>
<template>
    <section id="contact" class="py-20 px-4 bg-gradient-to-b from-slate-50 to-slate-100">
        <div class="container mx-auto">
          <div class="flex flex-col md:flex-row">
            <div class="md:w-1/2 mb-10 md:mb-0">
              <h2 class="text-3xl md:text-4xl font-bold mb-6">Get in Touch</h2>
              <p class="text-lg  mb-8">
                Have questions about Aithorix? Our team is here to help. Reach out to us and we'll get back to you as soon as possible.
              </p>

              <div class="space-y-6">
                <div class="flex items-start">
                  <div class="bg-purple-100 p-2 rounded-lg mr-4 flex-shrink-0">
                    <MapPin class="h-6 w-6 text-purple-600" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-1">Our Location</h3>
                    <p class="">123 Innovation Drive, Tech City, TC 12345</p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="bg-purple-100 p-2 rounded-lg mr-4 flex-shrink-0">
                    <Mail class="h-6 w-6 text-purple-600" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-1">Email Us</h3>
                    <p class="">info@aithorix.com</p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="bg-purple-100 p-2 rounded-lg mr-4 flex-shrink-0">
                    <Phone class="h-6 w-6 text-purple-600" />
                  </div>
                  <div>
                    <h3 class="text-xl font-semibold mb-1">Call Us</h3>
                    <p class="">+1 (555) 123-4567</p>
                  </div>
                </div>
              </div>

              <div class="flex space-x-4 mt-8">
                <a href="#" class="bg-purple-100 p-2 rounded-full hover:bg-purple-200 transition-colors">
                  <Twitter class="h-6 w-6 text-purple-600" />
                </a>
                <a href="#" class="bg-purple-100 p-2 rounded-full hover:bg-purple-200 transition-colors">
                  <Linkedin class="h-6 w-6 text-purple-600" />
                </a>
                <a href="#" class="bg-purple-100 p-2 rounded-full hover:bg-purple-200 transition-colors">
                  <Facebook class="h-6 w-6 text-purple-600" />
                </a>
                <a href="#" class="bg-purple-100 p-2 rounded-full hover:bg-purple-200 transition-colors">
                  <Instagram class="h-6 w-6 text-purple-600" />
                </a>
              </div>
            </div>

            <div class="md:w-1/2 md:pl-16">
              <div v-motion-slide-visible-right class="bg-white p-8 rounded-xl shadow-md">
                <h3 class="text-2xl font-semibold mb-6">Send Us a Message</h3>
                <form @submit.prevent="handleSubmit" class="space-y-6">
                  <div>
                    <TextField
                      id="name"
                      v-model="form.name"
                      placeholder="Your name"
                      label="Full Name"
                    />
                  </div>

                  <div>
                    <TextField
                      id="email"
                      v-model="form.email"
                      placeholder="your.email@example.com"
                      label="Email Address"
                    />
                  </div>

                  <div>
                    <TextField
                      id="subject"
                      v-model="form.subject"
                      placeholder="How can we help?"
                      label="Subject"
                    />
                  </div>

                  <div>
                    <TextField
                      id="message"
                      v-model="form.message"
                      placeholder="Your message"
                      label="Message"
                      type="textarea"
                    />
                  </div>

                  <button
                    type="submit"
                    class="btn-primary w-full"
                    :disabled="isSubmitting"
                  >
                    <span v-if="isSubmitting">Sending...</span>
                    <span v-else>Send Message</span>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
</template>