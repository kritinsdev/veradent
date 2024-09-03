// document.addEventListener('DOMContentLoaded', function () {
//     function updateSelectClasses(selectElement) {
//         const selectedOption = selectElement.options[selectElement.selectedIndex];
//
//         const optionBackgroundColor = window.getComputedStyle(selectedOption).backgroundColor;
//
//         selectElement.style.setProperty('background-color', optionBackgroundColor, 'important');
//
//         const optionColor = window.getComputedStyle(selectedOption).color;
//         selectElement.style.setProperty('color', optionColor, 'important');
//     }
//
//     function initializeSelectElements() {
//         const selectElements = document.querySelectorAll('.fi-select-input');
//         selectElements.forEach(selectElement => {
//             // Apply styles on page load
//             updateSelectClasses(selectElement);
//
//             // Apply styles whenever the selection changes
//             selectElement.addEventListener('change', function () {
//                 updateSelectClasses(selectElement);
//             });
//         });
//     }
//
//     initializeSelectElements();
//
//     // MutationObserver to handle dynamic additions to the DOM
//     const observer = new MutationObserver(() => {
//         initializeSelectElements();
//     });
//
//     observer.observe(document.body, { childList: true, subtree: true });
//
//     // Optional: Handle Livewire reactivity if using Livewire
//     if (window.Livewire) {
//         Livewire.hook('element.updated', () => {
//             initializeSelectElements();
//         });
//     }
// });
