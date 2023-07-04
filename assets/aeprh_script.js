jQuery(document).ready(function() {
    // When the value of the element with the ID 'aeprh-Select-all' changes
    jQuery(document).on('change', '#aeprh-Select-all', function() {
        // Get the value of the 'checked' property of the element with the class 'aeprh-single-delete'
        var isChecked = jQuery(this).prop('checked');

        // Set the 'checked' property of all elements with the class 'aeprh-single-delete' to the value obtained above
        jQuery(".aeprh-single-delete").prop('checked', isChecked);
    });
});
