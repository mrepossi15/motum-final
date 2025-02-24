import './bootstrap';

import './custom.js';
import '@fullcalendar/core/main.css';

import { createApp } from 'vue'
import GalleryModal from './components/GalleryModal.vue'

const app = createApp({})

app.component('GalleryModal', GalleryModal)

app.mount('#app')