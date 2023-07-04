jQuery(document).ready(function () {
    // When the value of the element with the ID 'aeprh-Select-all' changes
    jQuery(document).on('change', '#aeprh-Select-all', function () {
        // Get the value of the 'checked' property of the element with the class 'aeprh-single-delete'
        var isChecked = jQuery(this).prop('checked');

        // Set the 'checked' property of all elements with the class 'aeprh-single-delete' to the value obtained above
        jQuery(".aeprh-single-delete").prop('checked', isChecked);
    });

    // Attach a click event handler to the 'aeprh-delete-list' input element using event delegation
    jQuery(document).on('click', 'input[name="aeprh-delete-list"]', function() {
        // Select all checkboxes with the class 'aeprh-single-delete' that are checked
        var $checkedCheckboxes = jQuery('.aeprh-single-delete:checked');
        
        // Check if any checkboxes are checked
        if ($checkedCheckboxes.length > 0) {
            // Display a confirmation dialog and store the result
            var confirmResult = confirm("Are you sure you want to delete?");
            if (!confirmResult) {
                // If the user cancels the confirmation, cancel the delete operation
                return false;
            }
        } else {
            // If no checkboxes are checked, display an alert to prompt the user to select data
            alert('Please select data to delete!');
            return false;
        }
    });

});