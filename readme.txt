=== Paradiso LMS ===
Contributors: Paradiso Solutions
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 0.1

Wordpress - Paradiso  LMS integration plugin

##**Instruction to install the plugin**

Download the [auth-token](https://github.com/hersoncruz/moodle-auth-token) plugin for Moodle / Paradiso LMS.
Login to your Moodle / Paradiso LMS instance, install and enable the Authentication plugin.
Go to the menu: **Site Administration > Plugins > Authentication > Token Authentication** and set authentication code as some alphanumeric value in the **Salt Value** field. (The longer the Salt value the better.)

[Screen Shot 1](https://raw.githubusercontent.com/ParadisoSolutions/lms-wp-sso/master/img/moodle-config.png)

Go back to your WordPress instance and configure the **Moodle / Paradiso LMS** plugin. 
Paste the **Salt Value** you set in your Moodle / Paradiso instance.
Specify the URL to your Moodle / Paradiso LMS and save all changes by hitting the button.

[Screen Shot 2](https://raw.githubusercontent.com/ParadisoSolutions/lms-wp-sso/master/img/wp-configure-plugin-2.png)


After you save after hitting the button you’ll see the new link to do the SSO.

[Screen Shot 3](https://raw.githubusercontent.com/ParadisoSolutions/lms-wp-sso/master/img/final.png)

