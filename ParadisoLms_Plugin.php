<?php


include_once('ParadisoLms_LifeCycle.php');

class ParadisoLms_Plugin extends ParadisoLms_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31

        $retVal = array();

        # sso options
        {
            $retVal['sso_token_salt'] = __('Random PRIVATE string to encrypt communication between your Wordpress & LMS', 'paradiso-lms');
            $retVal['sso_lms_url'] = __('Specify the URL to your LMS instance e.g. ', 'paradiso-lms') . ' http://example.com';
            $retVal['sso_button_title'] = __('Title of the SSO button. The button will appear under Users menu', 'paradiso-lms');

            # locations
            {
                $label =__('Choose the location were you want to show the button', 'paradiso-lms');
                $locations = array($label, 'New left menu', 'Users menu', 'Dashboard menu', 'Links menu', 'Pages menu', 'Toolbar');
                $retVal['sso_button_location'] = $locations;
            }
            

            # get the capabilities
            $caps = $this->getCapabilities();
            if($caps)
            {
                $capLabel = __('To which capability will be visible the button?', 'paradiso-lms');
                $capVal = array($capLabel, 'Anyone', 'None');
                $capVal += array_values($caps);

                $retVal['sso_capability'] = $capVal;
            }
        }
        
        return $retVal;
    }


    function getCapabilities($humanName = false)
    {
        $caps = array();

        if(class_exists('URE_Lib'))
        {
            $lib = URE_Lib::get_instance();
            if(method_exists($lib, 'get_full_capabilities'))
            {
                $fullCapabilities = $lib->get_full_capabilities();
                foreach ($fullCapabilities as $key => $item)
                {
                    if('level_' == substr($key, 0, 6))
                    {
                        continue;
                    }

                    if($humanName)
                    {
                        $val = ('' == $item['human']) ? $key : $item['human'];
                    }
                    else
                    {
                        $val = $key;
                    }

                    $caps[$key] = $val;
                }
            }
        }

        return $caps;
    }



    protected function getOptionValueI18nString($optionValue) {
        $i18nValue = parent::getOptionValueI18nString($optionValue);
        return $i18nValue;
    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Paradiso LMS';
    }

    protected function getMainPluginFileName() {
        return 'paradiso-lms.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // add the sso link
        add_action('admin_menu', array(&$this, 'add_sso_menu_link'));
        add_action('admin_bar_menu', array(&$this, 'add_sso_menu_link'), 999);

        // add a js to allow open the link in a new tab
        wp_enqueue_script('add_sso_menu_links', plugins_url('/js/add_sso_menu_link.js', __FILE__));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false)
        {
            # colorbox
            {
                wp_enqueue_script('colorbox', plugins_url('/js/colorbox/jquery.colorbox-min.js', __FILE__));
                wp_enqueue_style('colorbox', plugins_url('/js/colorbox/1/colorbox.css', __FILE__));
            }
            
            #tabs
            {
                wp_enqueue_style('jquery-ui', plugins_url('/css/jquery-ui.css', __FILE__));
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-tabs');
            }

            # settings page
            {
                wp_enqueue_style('settings-page', plugins_url('/css/settings-page.css', __FILE__));
            }
        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));


        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }


    public function settingsPage() 
    {
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.', 'TEXT-DOMAIN'));
        }

        $optionMetaData = $this->getOptionMetaData();

        // Save Posted Options
        if($optionMetaData != null)
        {
            if(isset($_POST['setting_option']))
            {
                $prefix = $_POST['setting_option'];
                $prefixLen = strlen($prefix);
                foreach ($optionMetaData as $aOptionKey => $aOptionMeta)
                {
                    if($prefix != substr($aOptionKey, 0, $prefixLen))
                        continue;

                    if (isset($_POST[$aOptionKey]))
                    {
                        $this->updateOption($aOptionKey, $_POST[$aOptionKey]);
                    }
                }
            }
        }

        $settingsGroup = get_class($this) . '-settings-group';

        ?>
        <h2><?php echo $this->getPluginDisplayName(),' ', _e('Settings', 'paradiso-lms'); ?></h2>

        <?php if( (version_compare('5.2', phpversion())) > 0 || (version_compare('5.0', $this->getMySqlVersion()) > 0)) : ?>
        <div class="wrap">
            <table cellspacing="1" cellpadding="2">
                <tbody>
                    <tr>
                        <td>
                        <?php
                        if (version_compare('5.2', phpversion()) > 0) {
                            echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                            _e('(WARNING: This plugin may not work properly with versions earlier than PHP 5.2)', 'paradiso-lms');
                            echo '</span>';
                        }
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            if (version_compare('5.0', $this->getMySqlVersion()) > 0) {
                                echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                                _e('(WARNING: This plugin may not work properly with versions earlier than MySQL 5.0)', 'paradiso-lms');
                                echo '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif;?>
        
        <script type="text/javascript">
        jQuery(function($){
            
            var index = 0;

            if(window.location.hash)
            {
                index = $('#plugin_config_tabs a[href="' + window.location.hash + '"]').parent().index();
            }

            var s = {active:index};
            jQuery("#plugin_config_tabs").tabs(s).show();
        });
        </script>
        <div class="plugin_config">
            <div id="plugin_config_tabs" style="display:none">
                <ul>
                    <li><a href="#sso_plugin_config"><?php echo __('SSO', 'paradiso-lms')?></a></li>
                    <li><a href="#help_plugin_config"><?php echo __('Help', 'paradiso-lms')?></a></li>
                </ul>
                <div id="sso_plugin_config">
                    <?php $this->print_tab_options_form($optionMetaData, 'sso_')?>
                </div>
                <div id="help_plugin_config">
                    <ol>
                        <li>Download the <a target="_blank" href="https://github.com/hersoncruz/moodle-auth-token">auth-token</a> plugin for Moodle.</li>
                        <li>Login to your Moodle instance, install and enable the Autnetication plugin</li>
                        <li>
                            Go to the menu: <b>Site administration</b> >  <b>Plugins</b> > <b>Authentication</b> > <b>Token authentication</b><br>
                            and set some random words and numbers in the <b>Salt value</b> field. The longer the better<br>
                            <a class="colorbox" title="fdsfds" href="<?php echo plugins_url('/img/moodle-config.png', __FILE__);?>"><img src="<?php echo plugins_url('/img/moodle-config_.png', __FILE__);?>" /></a>
                        </li>
                        <li>
                            Go back to your wordpress instance and configure the <b>Paradiso LMS</b> plugin<br>
                            Paste the <b>Salt value</b> you just set in your Moodle instance<br>
                            Specify the url to your Moodle instance and hit button to save your changes<br>
                            <a class="colorbox" href="<?php echo plugins_url('/img/wp-configure-plugin-2.png', __FILE__);?>"><img src="<?php echo plugins_url('/img/wp-configure-plugin-2_.png', __FILE__);?>" /></a>
                        </li>
                        <li>
                            After you hit the button you'll see the new link to do the SSO<br>
                            <a class="colorbox" href="<?php echo plugins_url('/img/final.png', __FILE__);?>"><img src="<?php echo plugins_url('/img/final_.png', __FILE__);?>" /></a>
                        </li>
                    </ol>
                    <script>
                        jQuery(function($){
                            $("a.colorbox").colorbox();
                        });
                    </script>
                </div>
            </div>
        </div>
        <?php
    }


    function add_sso_menu_link($wp_admin_bar)
    {
        $lmsUrl = $this->getOption('sso_lms_url');
        $buttonLabel = $this->getOption('sso_button_title');
        $buttonLocation = $this->getOption('sso_button_location');
        $slug = 'paradiso-lms-sso';
        $callback = array(&$this, 'sso_page');

        $capability = $this->getOption('sso_capability');
        if('None' == $capability)
        {
            $capability = '';
        }
        elseif(empty($capability) || ('Anyone' == $capability))
        {
            $capability = 'read';
        }

        if(empty($lmsUrl) || empty($buttonLabel))
        {
            return;
        }
        

        if('Toolbar'  == $buttonLocation)
        {
            if(($wp_admin_bar instanceof WP_Admin_Bar) && is_admin_bar_showing())
            {
                # lets check te user capability
                if(current_user_can($capability))
                {
                    $args = array(
                        'id' => $slug,
                        'title' => $buttonLabel,
                        'href' => admin_url("/admin.php?page=$slug"), #=> http://example.net/wp/wp-admin/admin.php?page=paradiso-lms-sso
                        'meta' => array(
                            'title' => $buttonLabel,
                            'target' => '_blank',
                            'class' => 'dashicons-book-alt',
                        )
                    );

                    $wp_admin_bar->add_node($args);
                }
            }

            # add a hidden sub page to allow the Toolbar link accessing the callback function
            add_submenu_page('', 'Hidden', 'Hidden', $capability, $slug, $callback);
        }
        else
        {
            switch ($buttonLocation)
            {
                case 'New left menu':
                {
                    # add a top level page
                    add_menu_page('', $buttonLabel, $capability, $slug, $callback, 'dashicons-book-alt', 3);
                    break;
                }
                
                case 'Users menu':
                {
                    # under user profile page
                    add_users_page('', $buttonLabel, $capability, $slug, $callback);
                    break;
                }
                
                case 'Dashboard menu':
                {
                    add_dashboard_page('', $buttonLabel, $capability, $slug, $callback); 
                    break;
                }
                
                case 'Links menu':
                {
                    add_links_page('', $buttonLabel, $capability, $slug, $callback);
                    break;
                }

                case 'Pages menu':
                {

                    add_pages_page('', $buttonLabel, $capability, $slug, $callback);
                }
            }
        }
    }

   
    function sso_page()
    {
        if (is_user_logged_in())
        {
            $tokenSalt = $this->getOption('sso_token_salt');
            $lmsUrl = $this->getOption('sso_lms_url');

            $user = wp_get_current_user();

            $user_id = $user->id;

            $username = $user->user_login;
            $email = $user->user_email;
            $firstName = get_user_meta($user_id, 'first_name', true);
            $lastName = get_user_meta($user_id, 'last_name', true);
            $city = get_user_meta($user_id, 'billing_city', true);
            $country = get_user_meta($user_id, 'billing_country', true);

            if(empty($city))
                $city = 'city name';

            if(empty($country))
                $country = 'US';

            if (empty($firstName))
                $firstName = get_user_meta($user_id, 'billing_first_name', true);

            if (empty($lastName))
                $lastName = get_user_meta($user_id, 'billing_last_name', true);

            $errorStyles = 'border:1px solid;margin:10px 0px;padding:15px 10px 15px 50px;background-repeat:no-repeat;background-position:10px center;color:#D8000C;background-color:#FFBABA;';
            if(empty($firstName) || empty($lastName))
            {
                echo "<h1>Error</h1>";
                echo sprintf('<div style="%s">You have to set your first name, last name</div>', $errorStyles);
                return;
            }

            if(empty($lmsUrl))
            {
                echo "<h1>Error</h1>";
                echo sprintf('<div style="%s">You have to se the URL to your LMS instance.</div>', $errorStyles);
                return;
            }

            $capability = $this->getOption('sso_capability');
            if('None' == $capability)
            {
                echo "<h1>Error</h1>";
                echo sprintf('<div style="%s">You are not allowed to execute the SSO</div>', $errorStyles);
                return;
            }

            if(!empty($capability))
            {
                if('Anyone' != $capability)
                {
                    if(!current_user_can('update_core'))
                    {
                        echo "<h1>Error</h1>";
                        echo sprintf('<div style="%s">You are not allowed to execute the SSO</div>', $errorStyles);
                        return;
                    }
                }
            }

            $secretPart = $username . $email;
            $timestamp = time();

            $token = crypt($timestamp . $secretPart, $tokenSalt);

            #
            $data = array();
            $data['email'] = $email;
            $data['user'] = $username;
            $data['fn'] = $firstName;
            $data['ln'] = $lastName;
            $data['city'] = $city;
            $data['country'] = $country;
            $data['token'] = $token;
            $data['ts'] = $timestamp;
            $data['timestamp'] = $timestamp;

            $params = http_build_query($data, '&');
            
            $ssoUrl = "{$lmsUrl}/auth/token/index.php?{$params}";
            
            if(headers_sent())
            {
                echo sprintf('<meta http-equiv="refresh" content="1;url=%s">', $ssoUrl);
            }
            else
            {
                header("Location: $ssoUrl");
                exit;
            }
        }
    }


    function print_tab_options_form($optionMetaData, $prefix)
    {
        $prefixLen = strlen($prefix);
        ?>
        <form method="post" action="#<?php echo $prefix?>plugin_config">

            <?php settings_fields($settingsGroup); ?>

            <input type="hidden" name="setting_option" value="<?php echo $prefix?>">

            <?php if ($optionMetaData != null) : ?>
                <table class="plugin-options-table">
                    <tbody>
                        <?php
                        if ($optionMetaData != null)
                        {
                            foreach($optionMetaData as $aOptionKey => $aOptionMeta)
                            {
                                if($prefix != substr($aOptionKey, 0, $prefixLen))
                                    continue;
                                
                                $displayText = is_array($aOptionMeta) ? $aOptionMeta[0] : $aOptionMeta;
                                ?>
                                    <tr valign="top">
                                        <th scope="row"><p><label for="<?php echo $aOptionKey ?>"><?php echo $displayText ?></label></p></th>
                                        <td><?php $this->createFormControl($aOptionKey, $aOptionMeta, $this->getOption($aOptionKey)); ?></td>
                                    </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif;?>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'paradiso-lms') ?>"/>
            </p>

        </form>
        <?php
    }
}
