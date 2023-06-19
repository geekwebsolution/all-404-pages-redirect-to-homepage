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


?>
<div class="aeprh-main-box">
    <div class="aeprh-container">
        <div class="aeprh-header">
            <h1 class="aeprh-h1"> <?php _e('All 404 Redirect to Homepage', 'all-error-page-redirect-home'); ?></h1>
        </div>


        <div class="aeprh-option-section">

            <div class="aeprh-tabbing-box">
                <ul class="aeprh-tab-list">

                    <li><a href="?page=all-404-redirect-option" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>"><?php _e('General Option', 'mobile-message-for-woocommerce-enquiries-and-alerts'); ?></a></li>
                    <li><a href="?page=all-404-redirect-option&tab=aeprh-404-urls" class="nav-tab <?php if ($tab === 'aeprh-404-urls') : ?>nav-tab-active<?php endif; ?>"><?php _e('404 Logs', 'mobile-message-for-woocommerce-enquiries-and-alerts'); ?></a></li>

                </ul>
            </div>

            <?php
            if ($tab == null) {
            ?>
                <section class="aeprh-section">
                    <div class='aeprh_inner'>
                        <form method="POST">
                            <table class="form-table">
                                <tbody>

                                    <tr valign="top">
                                        <th scope="row">Status</th>
                                        <td>

                                            <select id="satus_404r" name="status">
                                                <option value="1" <?php if ($status == 1) { echo "selected"; } ?>>Enabled </option>
                                                <option value="0" <?php if ($status == 0) { echo "selected"; } ?>>Disabled </option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row">Redirect all 404 pages to: </th>
                                        <td>

                                            <input type="text" name="redirect_to" id="redirect_to" class="regular-text" value="<?php echo $redirect_to; ?>">
                                            <p class="description">Links that redirect for all 404 pages.</p>

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
                    <div class="aeprh-error-lists">

                        <table class="wp-list-table widefat striped">
                            <thead>

                                <tr>
                                    <th>#</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                    <th>URL</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                global $wpdb;
                                $table_name = $wpdb->prefix . "aeprh_links_lists";

                                $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

                                $limit = 20; 
                                $offset = ($pagenum - 1) * $limit;
                                $total = $wpdb->get_var("select count(*) as total from $table_name ORDER BY 'time' DESC");
                                $num_of_pages = ceil($total / $limit);

                                $rows = $wpdb->get_results("SELECT * from $table_name ORDER BY `time` DESC limit $offset, $limit");
                                // $rows = $wpdb->get_results("SELECT * from $table_name ORDER BY 'time' DESC  limit $offset, $limit");
                                $rowcount = $wpdb->num_rows;

                                ?>

                                <?php
                                if ($rowcount > 0) {
                                    $i = 1;
                                    foreach ($rows as $row) { ?>
                                        <tr>
                                            <td class="manage-column ss-list-width"><?php echo $i; ?></td>
                                            <td class="manage-column ss-list-width"><?php echo $row->ip_address; ?></td>
                                            <td class="manage-column ss-list-width"><?php echo $row->time; ?></td>
                                            <td class="manage-column ss-list-width"><a href="<?php echo $row->url; ?>" target="_blank" ><?php echo $row->url; ?></a></td> 
                                        </tr>
                                    <?php 
                                    $i++;
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                        <?php
                        $page_links = paginate_links( array(
                            'base'      => add_query_arg('pagenum', '%#%'),
							'current'   => max( 1, get_query_var('paged') ),
							'prev_next' => true,
                            'total'     => $num_of_pages,
                            'current'   => $pagenum,
                            'type'      => 'array',
                            'prev_text' => __('&laquo;', 'all-error-page-redirect-home'),
                            'next_text' => __('&raquo;', 'all-error-page-redirect-home'),
				
						) );

                
                        ?>
                        <div class="aeprh-pagination-sec">

                            <?php
                            if ( $page_links ) {
                                echo '<ul class="aeprh-page-numbers">';     
                                    echo '<li>';
                                        echo join( '</li><li>', $page_links );
                                    echo '</li>';                               
                                echo '</ul>';
                            }

                            ?>
                        </div>
                    </div>
                </section>
            <?php
            }
            ?>
        </div>
    </div>
</div>