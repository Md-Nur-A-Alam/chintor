import Alpine from 'alpinejs';
import { initMotion } from './motion';
import { themeSwitcher } from './theme';

window.Alpine = Alpine;

Alpine.data('themeSwitcher', themeSwitcher);

Alpine.start();

initMotion();
