<?php
if (isset($_POST['Save_Options'])) {
    $status = sanitize_text_field($_POST['status']);
    $redirect_to = sanitize_text_field($_POST['redirect_to']);
    $nonce = $_POST['_wpnonce'];
    if (wp_verify_nonce($nonce, 'r404option_nounce')) {
        update_option('status_404r', $status);
        update_option('redirect_to_404r', $redirect_to);
        success_option_msg_404r('Settings Saved!');
    } else {
        failure_option_msg_404r('Unable to save data!');
    }
}

$status = get_status_404r();

$redirect_to = get_redirect_to_404r();

$default_tab = null;
$tab = "";
$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;

if (isset($_POST['aeprh-delete-list']) && $_POST['aeprh-delete-list'] === 'Delete') {
    global $wpdb; // Access the global WordPress database object
    $table_name = $wpdb->prefix . "aeprh_links_lists"; // Table name in the WordPress database

    if (isset($_POST['list']) && is_array($_POST['list']) && !empty($_POST['list'])) {
        $rows_ids = implode(',', array_map('absint', $_POST['list'])); // Sanitize and convert the selected IDs to integers
        $result = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE ID IN ($rows_ids)")); // Use prepared statements to prevent SQL injection

        if ($result !== false) {
            success_option_msg_404r('Data Deleted Successfully!'); // Display a success message
        } else {
            failure_option_msg_404r('Unable to Delete Data!'); // Display a failure message
        }
    } else {
        failure_option_msg_404r('Please Select Data to Delete!'); // Display a failure message if no data was selected
    }
}

?>
<div class="aeprh-main-box">
    <div class="aeprh-container">
        <div class="aeprh-header">
            <h1 class="aeprh-h1"> <?php _e('All 404 Redirect to Homepage', 'all-404-pages-redirect-to-homepage'); ?></h1>
        </div>


        <div class="aeprh-option-section">

            <div class="aeprh-tabbing-box">
                <ul class="aeprh-tab-list">
                    <li><a href="?page=all-404-redirect-option" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>"><?php _e('General option', 'all-404-pages-redirect-to-homepage'); ?></a></li>
                    <li><a href="?page=all-404-redirect-option&tab=aeprh-404-urls" class="nav-tab <?php if ($tab === 'aeprh-404-urls') : ?>nav-tab-active<?php endif; ?>"><?php _e('404 Logs', 'all-404-pages-redirect-to-homepage'); ?></a></li>
                </ul>
            </div>

            <?php
            if ($tab == null) { ?>
                <section class="aeprh-section">
                    <div class='aeprh_inner'>
                        <form method="POST">
                            <table class="form-table">
                                <tbody>

                                    <tr valign="top">
                                        <th scope="row"><?php _e( 'Status', 'all-404-pages-redirect-to-homepage' ); ?></th>
                                        <td>

                                            <select id="satus_404r" name="status">
                                                <option value="1" <?php if ($status == 1) { echo "selected"; } ?>>Enabled </option>
                                                <option value="0" <?php if ($status == 0) { echo "selected"; } ?>>Disabled </option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row"><?php _e( 'Redirect all 404 pages to:', 'all-404-pages-redirect-to-homepage' ); ?> </th>
                                        <td>

                                            <input type="text" name="redirect_to" id="redirect_to" class="regular-text" value="<?php echo $redirect_to; ?>">
                                            <p class="description"><?php _e( 'Links that redirect for all 404 pages.', 'all-404-pages-redirect-to-homepage' ); ?></p>

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce = wp_create_nonce('r404option_nounce'); ?>" />
                            <input class="button-primary aeprh-submit" type="submit" value="Update" name="Save_Options">
                        </form>
                    </div>
                </section>
            <?php
            }
            if ($tab == "aeprh-404-urls") {
            ?>
                <section class="aeprh-section">
                    <form method="POST">
                        <div class="aeprh-error-lists">
                            <table class="wp-list-table widefat striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" name="aeprh-Select-all" value="all" id="aeprh-Select-all" class="aeprh-all-delete">
                                        </th>
                                        <th>#</th>
                                        <th><?php _e( 'IP Address', 'all-404-pages-redirect-to-homepage' ); ?></th>
                                        <th><?php _e( 'Date', 'all-404-pages-redirect-to-homepage' ); ?></th>
                                        <th><?php _e( 'URL', 'all-404-pages-redirect-to-homepage' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    global $wpdb; // Access the global WordPress database object
                                    $table_name = $wpdb->prefix . "aeprh_links_lists"; // Table name in the WordPress database

                                    $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1; // Get the current page number

                                    $limit = isset($_GET['limit']) ? absint($_GET['limit']) : 25; // Get the limit value for number of records per page

                                    $total = $wpdb->get_var("SELECT COUNT(*) as total FROM $table_name ORDER BY `time` DESC"); // Get the total number of records in the table
                                    $num_of_pages = ceil($total / $limit); // Calculate the total number of pages based on the limit
                                    if ($pagenum > $num_of_pages) $pagenum = 1; // Set the current page number to 1 if it exceeds the total number of pages
                                    $offset = ($pagenum - 1) * $limit; // Calculate the offset for pagination

                                    $rows = $wpdb->get_results("SELECT * FROM $table_name ORDER BY `time` DESC LIMIT $offset, $limit"); // Fetch the records for the current page
                                    $rowcount = count($rows); // Get the number of fetched records

                                    if ($rowcount > 0) {
                                        $i = ($limit * ($pagenum - 1)) + 1; // Initialize the counter for the displayed row number

                                        foreach ($rows as $row) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="list[]" value="<?php echo $row->id; ?>" class="aeprh-single-delete">
                                                </td>
                                                <td class="manage-column ss-list-width"><?php echo $i; ?></td>
                                                <td class="manage-column ss-list-width"><?php echo $row->ip_address; ?></td>
                                                <td class="manage-column ss-list-width"><?php echo $row->time; ?></td>
                                                <td class="manage-column ss-list-width">
                                                    <a href="<?php echo $row->url; ?>" target="_blank"><?php echo $row->url; ?></a>
                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'  class='aeprh-not-data'>No records found</td></tr>";
                                    }
                                    ?>
                                </tbody>

                            </table>

                            
                            <div class="aeprh-action">
                                <div class="aeprh-remove-action">
                                    <input type="submit" name="aeprh-delete-list" value="Delete" class="button-primary aeprh-submit">
                                </div>
                                <div class="aeprh-pagination-sec">
                                    <div class="aeprh-page-limit-sec">
                                  
                                        <select name="aeprh-page-limit" class="aeprh-page-limit" onchange="location = this.value">
                                            <?php
                                            // Define the base URL using $_SERVER['REQUEST_URI']
                                            $baseURL = $_SERVER['REQUEST_URI'] . '&limit=';
                                            
                                            // Define the available limit options and their respective values
                                            $limitOptions = array(
                                                10 => '10',
                                                25 => '25',
                                                50 => '50',
                                                100 => '100'
                                            );

                                            // Loop through the limit options and generate the corresponding <option> elements
                                            foreach ($limitOptions as $value => $label) {
                                                // Check if the current limit matches the option value and add the 'selected' attribute if true
                                                $selected = ($limit == $value) ? 'selected' : '';
                                                
                                                // Generate the URL for the option using the base URL and the current limit value
                                                $optionURL = $baseURL . $value . '&pagenum=1';
                                                
                                                // Output the <option> element with the appropriate value, label, and selected attribute
                                                echo "<option value=\"$optionURL\" $selected>$label</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="pagination-links">  
                                        <?php
                                        $page_links = paginate_links( array(
                                            'base'      => add_query_arg('pagenum', '%#%'),
                                            'current'   => max( 1, get_query_var('paged') ),
                                            'prev_next' => true,
                                            'total'     => $num_of_pages,
                                            'current'   => $pagenum,
                                            'type'      => 'array',
                                            'prev_text' => '&laquo;',
                                            'next_text' => '&raquo;',
                                
                                        ) );
                                        if ( $page_links ) {
                                            echo sprintf( '<ul class="aeprh-page-numbers"><li>%s</li></ul>', join( '</li><li>', $page_links ) );
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
                <?php
            }
            ?>
        </div>
    </div>
</div>
