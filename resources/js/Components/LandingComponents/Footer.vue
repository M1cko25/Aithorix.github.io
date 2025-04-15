<script setup>
import { Twitter, Linkedin, Facebook, Instagram, BrainCircuit } from 'lucide-vue-next';
import Logo from '@/images/Logo2.png';
</script>
<template>
    <footer class="bg-slate-900 text-white py-16 px-4">
        <div class="container mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
              <div class="flex items-center space-x-2 mb-6">
                <img :src="Logo" alt="Aithorix Logo" class="h-10 w-30" />
              </div>
              <p class="text-slate-400 mb-6">
                Intelligent project management powered by AI to help teams achieve more.
              </p>
              <div class="flex space-x-4">
                <a href="#" class="text-slate-400 hover:text-white transition-colors">
                  <Twitter class="h-5 w-5" />
                </a>
                <a href="#" class="text-slate-400 hover:text-white transition-colors">
                  <Linkedin class="h-5 w-5" />
                </a>
                <a href="#" class="text-slate-400 hover:text-white transition-colors">
                  <Facebook class="h-5 w-5" />
                </a>
                <a href="#" class="text-slate-400 hover:text-white transition-colors">
                  <Instagram class="h-5 w-5" />
                </a>
              </div>
            </div>
            
            <div>
              <h3 class="text-lg font-semibold mb-6">Product</h3>
              <ul class="space-y-4">
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Features</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Templates</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Integrations</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Pricing</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Changelog</a></li>
              </ul>
            </div>
            
            <div>
              <h3 class="text-lg font-semibold mb-6">Resources</h3>
              <ul class="space-y-4">
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Documentation</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Tutorials</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Blog</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Community</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">API Reference</a></li>
              </ul>
            </div>
            
            <div>
              <h3 class="text-lg font-semibold mb-6">Company</h3>
              <ul class="space-y-4">
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">About Us</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Careers</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Contact</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Privacy Policy</a></li>
                <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Terms of Service</a></li>
              </ul>
            </div>
          </div>
          
          <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-slate-400 mb-4 md:mb-0">
              &copy; 2025 Aithorix. All rights reserved.
            </p>
            <div class="flex space-x-6">
              <a href="#" class="text-slate-400 hover:text-white transition-colors">Privacy Policy</a>
              <a href="#" class="text-slate-400 hover:text-white transition-colors">Terms of Service</a>
              <a href="#" class="text-slate-400 hover:text-white transition-colors">Cookie Policy</a>
            </div>
          </div>
        </div>
      </footer>
</template>