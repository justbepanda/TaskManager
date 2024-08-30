import './bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;

import Alpine from 'alpinejs';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

window.Alpine = Alpine;

Alpine.start();

import ujs from '@rails/ujs';
ujs.start();


