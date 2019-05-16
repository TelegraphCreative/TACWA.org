var validate = new Bouncer('[data-validate]', {

   customValidations: {
      valueMismatch: function (field) {
         // Look for a selector for a field to compare
         // If there isn't one, return false (no error)
         var selector = field.getAttribute('data-bouncer-match');
         if (!selector) return false;

         // Get the field to compare
         var otherField = field.form.querySelector(selector);
         if (!otherField) return false;

         // Compare the two field values
   		// We use a negative comparison here because if they do match, the field validates
   		// We want to return true for failures, which can be confusing
   		return otherField.value !== field.value;
      }
   },

   // Message Settings
   messageCustom: 'data-bouncer-message', // The data attribute to use for customer error messages

   // Error messages by error type
   messages: {
      missingValue: {
         checkbox: 'This field is required.',
         radio: 'Please select a value.',
         select: 'Please select a value.',
         text: 'Please fill out this field',
         number: 'Please fill out this field',
         email: 'Please enter an email address',
         password: 'Please enter a password',
         default: 'Please make a selection'
      },
      patternMismatch: {
         email: 'Please enter a valid email address.',
         url: 'Please enter a URL.',
         number: 'Please enter a number',
         color: 'Please match the following format: #rrggbb',
         date: 'Please use the YYYY-MM-DD format',
         time: 'Please use the 24-hour time format. Ex. 23:00',
         month: 'Please use the YYYY-MM format',
         default: 'Please match the requested format.'
      },
      valueMismatch: function (field) {
         var customMessage = field.getAttribute('data-bouncer-mismatch-message');
         return customMessage ? customMessage : 'Please make sure passwords match.'
      }
   }
});