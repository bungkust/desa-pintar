/**
 * Main JavaScript Entry Point
 * Import all JavaScript modules here
 */

// Import navbar functionality
import './navbar.js';

// Chart.js is dynamically loaded only on APBDes page - not imported here

// Alpine.js Error Suppression for Livewire/Filament
document.addEventListener('alpine:init', () => {
    // Suppress Alpine.js errors related to Livewire component DOM tree
    console.log('Alpine.js initialized with error suppression');
});

window.addEventListener('error', (event) => {
    // Suppress Livewire component DOM tree errors
    if (event.error && event.error.message &&
        event.error.message.includes('Could not find Livewire component in DOM tree')) {
        event.preventDefault();
        console.warn('Suppressed Alpine.js DOM tree error (non-critical)');
        return false;
    }
});

