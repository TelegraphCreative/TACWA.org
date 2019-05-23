//
// Reveal Modal
//

var modalTrigger   = document.querySelectorAll('.js--deactivate-trigger');
var modal = document.querySelector('.js--deactivate-modal');
var modalClose = document.querySelector('.js--modal__close-btn');
var modalCancel = document.querySelector('.js--modal__cancel-btn');

modalTrigger.forEach(function(revealModal) {
   revealModal.onclick = function(){
      modal.classList.add('--is-visible');
   };
});

modalClose.onclick = function(){
   modal.classList.remove('--is-visible')
};

modalCancel.onclick = function(){
   modal.classList.remove('--is-visible')
};