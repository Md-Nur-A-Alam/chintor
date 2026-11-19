import Alpine from 'alpinejs';
import { themeSwitcher } from './theme';

window.Alpine = Alpine;

Alpine.data('themeSwitcher', themeSwitcher);

Alpine.start();
