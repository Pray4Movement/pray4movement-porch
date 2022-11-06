<?php

class Pray4Movement_Site_Porch_Admin {
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct() {
        if ( ! is_admin() ) {
            return;
        }

        add_filter( 'dt_remove_menu_pages', [ $this, 'add_media_tab' ], 10, 1 );
        add_filter( 'upload_mimes', [ $this, 'add_additional_mime_types' ], 1, 1 );

        if ( '/wp-admin/upload.php' === $_SERVER['REQUEST_URI'] ) {
            $this->p4m_add_media_page_warning();
        }

        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    public function add_media_tab( $list ) {
        if ( isset( $list['media'] ) ) {
            unset( $list['media'] );
        }
        return $list;
    }
    public function add_additional_mime_types( $mime_types){
        $mime_types['svg'] = 'image/svg+xml'; //Adding svg extension
        $mime_types['psd'] = 'image/vnd.adobe.photoshop'; //Adding photoshop files
        $mime_types['pdf'] = 'application/pdf'; //Adding photoshop files
        $mime_types['docx'] = 'application/vnd.openxmlformats-'; //Adding photoshop files
        $mime_types['doc'] = 'application/msword'; //Adding photoshop files
        $mime_types['csv'] = 'text/csv'; //Adding photoshop files
        $mime_types['zip'] = 'application/zip'; //Adding photoshop files
        return $mime_types;
    }

    public function p4m_add_media_page_warning() {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>SECURITY WARNING: <BR>ALL IMAGES AND MEDIA FILES ADDED HERE ARE PUBLICLY ACCESSIBLE TO THE INTERNET. <BR>DO NOT STORE SENSITIVE FILES!</p>
        </div>
        <?php
    }

    public function admin_menu() {
        add_menu_page( 'Front Porch', 'Front Porch', 'manage_dt', 'landing_page', [ $this, 'landing_admin_page' ], 'dashicons-admin-generic', 70 );
    }

    public function landing_admin_page(){
        $slug = 'landing_page';

        if ( !current_user_can( 'manage_options' ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.' ) );
        }

        if ( isset( $_GET["tab"] ) ) {
            $tab = sanitize_key( wp_unslash( $_GET["tab"] ) );
        } else {
            $tab = 'settings';
        }

        $link = 'admin.php?page='.$slug.'&tab=';

        ?>
        <div class="wrap">
            <h2>Pray4Movement Landing Page</h2>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_attr( $link ) . 'settings' ?>"
                   class="nav-tab <?php echo esc_html( ( $tab == 'settings' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">Settings
                </a>
                <a href="<?php echo esc_attr( $link ) . 'ipstack' ?>"
                   class="nav-tab <?php echo esc_html( ( $tab == 'ipstack' ) ? 'nav-tab-active' : '' ); ?>">IpStack
                </a>

            </h2>

            <?php
            switch ($tab) {
                case "settings":
                    $this->settings();
                    break;
                case "ipstack":
                    $this->ipstack();
                    break;
                default:
                    break;
            }
            ?>

        </div><!-- End wrap -->
        <?php
    }

    public function settings(){

        $defaults = DT_Posts::get_post_field_settings( 'contacts' );

        $content = p4m_porch_fields();
        if ( isset( $_POST['landing_page'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['landing_page'] ) ), 'landing_page'.get_current_user_id() ) ) {

            if ( isset( $_POST['reset'] ) ) {
                delete_option('landing_content' );
                $content = p4m_porch_fields();
            }
            else {
                foreach( $content as $index => $value ) {
                    if ( isset( $_POST[$index] ) && (
                        $value['type'] === 'text' ||
                        $value['type'] === 'source_for_subscriptions' ||
                        $value['type'] === 'assigned_user_for_followup' ||
                        $value['type'] === 'select'
                        ) ) {
                        $content[$index]['value'] = sanitize_text_field( wp_unslash( $_POST[$index] ) );
                    }
                    if ( isset( $_POST[$index] ) && $value['type'] === 'textarea' ) {
                        $content[$index]['value'] = wp_unslash( $_POST[$index] );
                    }
                }
            }




//            if ( isset( $_POST['title'] ) ) {
//                $content['title']['value'] = sanitize_text_field( wp_unslash( $_POST['title'] ) );
//            }
//            if ( isset( $_POST['description'] ) ) {
//                $content['description']['value'] = sanitize_text_field( wp_unslash( $_POST['description'] ) );
//            }
//            if ( isset( $_POST['location'] ) ) {
//                $content['location']['value'] = sanitize_text_field( wp_unslash( $_POST['location'] ) );
//            }
//            if ( isset( $_POST['logo_url'] ) ) {
//                $content['logo_url']['value'] = sanitize_text_field( wp_unslash( $_POST['logo_url'] ) );
//            }
//            if ( isset( $_POST['background_image_url'] ) ) {
//                $content['background_image_url']['value'] = sanitize_text_field( wp_unslash( $_POST['background_image_url'] ) );
//            }
//            if ( isset( $_POST['facebook_url'] ) ) {
//                $content['facebook_url']['value'] = sanitize_text_field( wp_unslash( $_POST['facebook_url'] ) );
//            }
//            if ( isset( $_POST['facebook_events_url'] ) ) {
//                $content['facebook_events_url']['value'] = sanitize_text_field( wp_unslash( $_POST['facebook_events_url'] ) );
//            }
//            if ( isset( $_POST['instagram_url'] ) ) {
//                $content['instagram_url']['value'] = sanitize_text_field( wp_unslash( $_POST['instagram_url'] ) );
//            }
//            if ( isset( $_POST['twitter_url'] ) ) {
//                $content['twitter_url']['value'] = sanitize_text_field( wp_unslash( $_POST['twitter_url'] ) );
//            }
//            if ( isset( $_POST['mailchimp_form_url'] ) ) {
//                $content['mailchimp_form_url']['value'] = sanitize_text_field( wp_unslash( $_POST['mailchimp_form_url'] ) );
//            }
//            if ( isset( $_POST['mailchimp_form_hidden_id'] ) ) {
//                $content['mailchimp_form_hidden_id']['value'] = sanitize_text_field( wp_unslash( $_POST['mailchimp_form_hidden_id'] ) );
//            }
//            if ( isset( $_POST['contact_form'] ) ) {
//                $content['contact_form']['value'] = wp_unslash( $_POST['contact_form'] );
//            }
//
//            if ( isset( $_POST['samples_section'] ) ) {
//                $content['samples_section']['value'] = sanitize_text_field( wp_unslash( $_POST['samples_section'] ) );
//            }
//            if ( isset( $_POST['who_email'] ) ) {
//                $content['who_email']['value'] = sanitize_text_field( wp_unslash( $_POST['who_email'] ) );
//            }
//            if ( isset( $_POST['who_facebook'] ) ) {
//                $content['who_facebook']['value'] = sanitize_text_field( wp_unslash( $_POST['who_facebook'] ) );
//            }
//            if ( isset( $_POST['who_facebook_events'] ) ) {
//                $content['who_facebook_events']['value'] = sanitize_text_field( wp_unslash( $_POST['who_facebook_events'] ) );
//            }
//            if ( isset( $_POST['who_training'] ) ) {
//                $content['who_training'] = sanitize_text_field( wp_unslash( $_POST['who_training'] ) );
//            }
//            if ( isset( $_POST['who_whatsapp'] ) ) {
//                $content['who_whatsapp'] = sanitize_text_field( wp_unslash( $_POST['who_whatsapp'] ) );
//            }
//            if ( isset( $_POST['who_signal'] ) ) {
//                $content['who_signal'] = sanitize_text_field( wp_unslash( $_POST['who_signal'] ) );
//            }
//
//
//            if ( isset( $_POST['stats_population'] ) ) {
//                $content['stats_population'] = sanitize_text_field( wp_unslash( $_POST['stats_population'] ) );
//            }
//            if ( isset( $_POST['stats_cities'] ) ) {
//                $content['stats_cities'] = sanitize_text_field( wp_unslash( $_POST['stats_cities'] ) );
//            }
//            if ( isset( $_POST['stats_trainings'] ) ) {
//                $content['stats_trainings'] = sanitize_text_field( wp_unslash( $_POST['stats_trainings'] ) );
//            }
//            if ( isset( $_POST['stats_churches'] ) ) {
//                $content['stats_churches'] = sanitize_text_field( wp_unslash( $_POST['stats_churches'] ) );
//            }
//            if ( isset( $_POST['google_analytics'] ) ) {
//                $content['google_analytics'] = wp_unslash( $_POST['google_analytics'] );
//            }
//
//            if ( isset( $_POST['mailchimp_api_key'] ) ) {
//                $content['mailchimp_api_key'] = sanitize_text_field( wp_unslash( $_POST['mailchimp_api_key'] ) );
//            }
//            if ( isset( $_POST['mailchimp_list_id'] ) ) {
//                $content['mailchimp_list_id'] = sanitize_text_field( wp_unslash( $_POST['mailchimp_list_id'] ) );
//            }
//            if ( isset( $_POST['mailchimp_form_url'] ) ) {
//                $content['mailchimp_form_url'] = sanitize_text_field( wp_unslash( $_POST['mailchimp_form_url'] ) );
//            }
//            if ( isset( $_POST['mailchimp_form_hidden_id'] ) ) {
//                $content['mailchimp_form_hidden_id'] = sanitize_text_field( wp_unslash( $_POST['mailchimp_form_hidden_id'] ) );
//            }
//            if ( isset( $_POST['status_for_subscriptions'] ) ) {
//                $content['status_for_subscriptions'] = sanitize_text_field( wp_unslash( $_POST['status_for_subscriptions'] ) );
//            }
//            if ( isset( $_POST['source_for_subscriptions'] ) ) {
//                $content['source_for_subscriptions'] = sanitize_text_field( wp_unslash( $_POST['source_for_subscriptions'] ) );
//            }
//            if ( isset( $_POST['assigned_user_for_followup'] ) ) {
//                $content['assigned_user_for_followup'] = sanitize_text_field( wp_unslash( $_POST['assigned_user_for_followup'] ) );
//            }

            update_option( 'landing_content', $content, true );
            $content = p4m_porch_fields();
        }
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <!-- Box -->
                        <form method="post">
                            <?php wp_nonce_field( 'landing_page'.get_current_user_id(), 'landing_page' ) ?>
                            <table class="widefat striped">
                                <thead>
                                <tr>
                                    <th colspan="2">Configuration</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach( $content as $i => $value ) {
                                    if ( $value['type'] === 'text' ) {
                                        ?>
                                        <tr>
                                            <td style="width:150px;">
                                                <?php echo esc_html( $value['label']) ?>
                                            </td>
                                            <td>
                                                <input type="text" name="<?php echo esc_html( $i ) ?>" class="regular-text" value="<?php echo esc_html( $value['value']) ?>" />
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    else if ( $value['type'] === 'textarea' ) {
                                        ?>
                                        <tr>
                                            <td style="width:150px;">
                                                <?php echo esc_html( $value['label']) ?>
                                            </td>
                                            <td>
                                                <textarea type="text" name="<?php echo esc_html( $i ) ?>" class="regular-text" ><?php echo $value['value'] ?></textarea>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    else if ( $value['type'] === 'select' ) {
                                        ?>
                                        <tr>
                                            <td style="width:150px;">
                                                <?php echo esc_html( $value['label']) ?>
                                            </td>
                                            <td>
                                                <select name="<?php echo esc_html( $i ) ?>">
                                                    <option value="<?php echo esc_html( $value['value'] ) ?>"><?php echo $value['defaults'][$value['value']] ?? '' ?></option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php
                                    }

                                }
                                ?>


                                <tr>
                                    <td style="width:150px;">
                                        Source for Newsletter Subscriptions
                                    </td>
                                    <td>
                                        <select name="source_for_subscriptions" id="source_for_subscriptions">
                                            <?php
                                            foreach ( $defaults['sources']['default'] as $index => $item ) {
                                                if ( $content['source_for_subscriptions']['value'] === $index ) {
                                                    ?>
                                                    <option value="<?php echo esc_attr( $index ) ?>" selected ><?php echo esc_html( $item['label'] ) ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value="<?php echo esc_attr( $index ) ?>"><?php echo esc_html( $item['label'] ) ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:150px;">
                                        Assigned User for Follow-up Leads
                                    </td>
                                    <td>
                                        <?php
                                        $roles = [];
                                        $wp_roles       = wp_roles()->roles;
                                        $selected_value = $content['assigned_user_for_followup']['value'];

                                        foreach ( $wp_roles as $role_name => $role_obj ) {
                                            if ( ! empty( $role_obj['capabilities']['access_contacts'] ) ) {
                                                $roles[] = $role_name;
                                            }
                                        }

                                        $potential_user_list = get_users(
                                            [
                                                'order'    => 'ASC',
                                                'orderby'  => 'display_name',
                                                'role__in' => $roles,
                                            ]
                                        );

                                        $base_user           = dt_get_base_user();

                                        ?>
                                        <select name="assigned_user_for_followup" id="assigned_user_for_followup">
                                            <option disabled>---</option>
                                            <?php foreach ( $potential_user_list as $potential_user ): ?>
                                                <option
                                                    value="<?php echo esc_attr( $potential_user->ID ); ?>" <?php if ( $potential_user->ID == $selected_value || ! $selected_value && $potential_user->ID == $base_user->ID ): ?> selected <?php endif; ?> ><?php echo esc_attr( $potential_user->display_name ); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <button type="submit" class="button">Update</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <br>

                            <table class="widefat striped">
                                <thead>
                                <tr>
                                    <th colspan="2">Reset</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="2">
                                        <button type="submit" class="button" name="reset" value="true">Reset</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </form>
                        <!-- End Box -->
                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->
                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function ipstack() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-1">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php DT_Ipstack_API::metabox_for_admin(); ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }
}
Pray4Movement_Site_Porch_Admin::instance();
