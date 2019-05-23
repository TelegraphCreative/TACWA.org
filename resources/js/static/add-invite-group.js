var addBtn   = document.querySelector('.js--add-button');
var counter = 5

addBtn.onclick = function(){
   // Gimme 3
   for (var i = 1; i <= 3; ++i) {
      counter ++;
      // Find group to copy
      var group = document.querySelector('.invite-group');
      // Find container to append to
      var container = document.querySelector('.js--invite-container');
      // Copy children too
      var clone = group.cloneNode(true);


      // Name Fieldgroup - Label 'for' Attribute
      clone.firstElementChild.firstElementChild.setAttribute('for', 'name--' + counter++);
      // Name Fieldgroup - Input 'id' Attribute
      clone.firstElementChild.firstElementChild.nextElementSibling.setAttribute('id', 'name--' + --counter);
      // Name Fieldgroup - Input 'name' Attribute
      clone.firstElementChild.firstElementChild.nextElementSibling.setAttribute('name', 'name--' + counter);

      // Email Fieldgroup - Label 'for' Attribute
      clone.firstElementChild.nextElementSibling.firstElementChild.setAttribute('for', 'email--' + counter++);
      // Email Fieldgroup - Input 'id' Attribute
      clone.firstElementChild.nextElementSibling.firstElementChild.nextElementSibling.setAttribute('id', 'email--' + --counter);
      // Email Fieldgroup - Input 'name' Attribute
      clone.firstElementChild.nextElementSibling.firstElementChild.nextElementSibling.setAttribute('name', 'email--' + counter);

      // Add new group to end of container
      container.appendChild(clone);
   }
}