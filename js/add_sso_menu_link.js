if(undefined !== window.jQuery)
{
    jQuery(function($){
        $('.wp-submenu a[href="users.php?page=paradiso-lms-sso"]').attr("target", "_blank");
        $('.wp-submenu a[href="profile.php?page=paradiso-lms-sso"]').attr("target", "_blank");
        $('#adminmenu a[href="index.php?page=paradiso-lms-sso"]').attr("target", "_blank");
        $('#menu-links a[href="edit-tags.php?taxonomy=link_category&page=paradiso-lms-sso"]').attr("target", "_blank");
        $('#menu-pages a[href="edit.php?post_type=page&page=paradiso-lms-sso"]').attr("target", "_blank");
        $('#toplevel_page_paradiso-lms-sso a[href="admin.php?page=paradiso-lms-sso"]').attr("target", "_blank");
    });
}