<?php
 /*
  * Plugin Name: Minimum Version
  * Description: Shows the minimum version of Wordpress that a plugin requires
  * Version: 0.1
  * Author: Steven Almeroth
  * Author URI: http://warriorship.org/sma/
  * License: GPL2
  */

 /*  Copyright 2011  Steven Almeroth  (sroth77@gmail.com)
  *
  *   This program is free software; you can redistribute it and/or modify
  *   it under the terms of the GNU General Public License as published by
  *   the Free Software Foundation; either version 2 of the License, or
  *   (at your option) any later version.
  *
  *   This program is distributed in the hope that it will be useful,
  *   but WITHOUT ANY WARRANTY; without even the implied warranty of
  *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *   GNU General Public License for more details.
  *
  *   You should have received a copy of the GNU General Public License
  *   along with this program; if not, write to the Free Software
  *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
  */
define ('MINIMUMVERSION_TEXTDOMAIN', 'minimum-version');
define ('BHOOKS', file_get_contents(dirname(__FILE__).'/hooks-3.json'));

if (!class_exists("MinimumVersion"))
{
    class MinimumVersion
    {
        /**
         * Constructor
         *
         * Get settings from database and call init()
         */
        function __construct ( )
        {
            $this-> hooks = json_decode(BHOOKS);
        }

        function admin_init ( )
        {
            # pass
        }

        function admin_menu ( )
        {
            // if( !function_exists('current_user_can')
            //  || !current_user_can('manage_options') )
            //     return;

            add_plugins_page(
                __('Minimum Version', MINIMUMVERSION_TEXTDOMAIN),
                'Minimum Version',
                'read',
                MINIMUMVERSION_TEXTDOMAIN.'-plugins',
                array($this, '_plugins_page')
            );

            // add_options_page(
            //     __('Minimum Version', MINIMUMVERSION_TEXTDOMAIN),
            //     'Minimum Version',
            //     'manage_options',
            //     MINIMUMVERSION_TEXTDOMAIN.'-settings',
            //     array($this, '_options_page')
            // );
        }

        function _options_page ( )
        {
            ?>
                <div class="wrap">
                    <?php screen_icon(); ?>
                    <h2> Minimum Version </h2>
                    <form action="options.php" method="post">
                        <?php settings_fields(MINIMUMVERSION_TEXTDOMAIN); ?>
                        <?php do_settings_sections(__FILE__); ?>
                        <p class="submit">
                            <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
                        </p>
                    </form>
                </div>
            <?php
        }

        function _plugins_page ( )
        {
            ?>
                <div class="wrap minver">
                    <style type="text/css">
                        div.minver ul {
                            list-style-type: disc;
                            padding-left: 100px;
                        }
                    </style>

                    <?php screen_icon(); ?>
                    <h2> Minimum Version </h2>

                    <p> This plugin looks through all the plugins on this site
                        to determine what Wordpress version is needed to run
                        each one. </p>

                    <p> The following limitations are currently in place: </p>
                    <ul>
                        <li> Only determines if Wordpress version 3.0 is required or not </li>
                        <li> Only looks in the standard plugin directory </li>
                        <li> Only looks at the main plugin file </li>
                        <li> Only looks at hooks, not functions </li>
                        <li> The included hooks list may be out of date and/or incomplete </li>
                    </ul>

                    <h3> Found plugins: </h3>
                    <ul>
            <?php
            foreach (get_plugins() as $path => $data) {
                $wp_version = $this-> _get_wpversion($path);
                echo <<< LI
                    <li>
                        <b>{$data['Name']}</b> by <em>{$data['Author']}</em>:
                        $wp_version
                    </li>
LI;
            }
            ?>
                    </ul>
                    <!-- pre>
                      <?php print_r(get_plugins()); ?>
                    </pre -->
                </div>
            <?php
        }

        /**
         * Calculate the minimum Wordpres version
         *
         * @stub: this function does not contain the actual logic yet.
         */
        function _get_wpversion ( $path )
        {
            $main_plugin_file = file_get_contents(WP_PLUGIN_DIR.'/'.$path);
            foreach ($this-> hooks as $hook) {
                if (strpos($main_plugin_file, $hook) !== false) {
                    return 'at least Wordpress 3.0 required';
                }
             }
             return 'Wordpress version 3 not required';
        }

    } //End Class MinimumVersion

} //End class_exists check

if (class_exists("MinimumVersion")) {
    $minimum_version_instance = new MinimumVersion();

    add_action('admin_init', array($minimum_version_instance, 'admin_init'));
    add_action('admin_menu', array($minimum_version_instance, 'admin_menu'));
}
