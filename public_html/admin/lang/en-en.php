<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * LANGUAGE FILE :: ENGLISH :: EN-EN
 * 
 */
return [
    
    "id" => "English",

    "nav.home" => "Home",
    "nav.apps" => "Apps",
    "nav.about" => "About ...",

    "nav.users" => "Users",
    "nav.config" => "Configuration",

    // ----- page :: home
    "home.welcome" => "Welcome to <strong>Axels ObjManager</strong>",
    "home.banner" => "Backend to edit objects of multiple applications.",
    "home.apps" => "Count of apps",
    "home.objects" => "Objects",
    "home.objecttypes"=> "Object types",
    "home.items"=> "Items",

    // ----- page :: app home
    "apphome.title" => "Backend",

    // ----- page :: configuration
    "globalconfig.title" => "Global Configuration",
    "globalconfig.banner" => "Edit global configuration of this backend: language, users with its roles and more",

    // ----- page :: app config
    "config.title" => "Application configuration",
    "config.banner" => "Edit configuration of this application: meta data, database connnection, objects.",

    // ----- page :: about
    "about.title" => "About this backend",
    "about.text" => '
        Author: <strong>Axel Hahn</strong><br>
        Programming langauge: PHP 8<br>
        License: GNU GPL 3.0<br>
        Sourcecode: Github<br>
        Documentation: axel-hahn.de/docs/<br>
        <br>
        <strong>Used Products</strong>:<br>
        Without the inclusion of other products, development takes much longer.<br>
        My thanks go to the developers of the following projects:
        <ul>
            <li><a href="https://adminlte.io/">AdminLTE</a> - Bootstrap Admin Template
                <ul>
                <li><a href="https://summernote.org/">Summernote</a> - Summmernote (HTML-Editor)</li>
                <li><a href="https://github.com/snapappointments/bootstrap-select">Bootstrap-Select plugin</a></li>
                </ul>
            <li><a href="https://jquery.com/">jQuery</a></li>
            <li><a href="https://fontawesome.com/">Fontawesome</a> (Icons)</li>
            <li><a href="https://os-docs.iml.unibe.ch/adminlte-renderer/">AdminLTE-Renderer - class</a> (Institut for Medical Education; University of Bern)</li>
            <li><a href="https://github.com/axelhahn/php-abstract-dbo">php-abstract-dbo </a> - Axels Abstract PDO class (Axel Hahn)</li>
        </ul>
        ',

    // ----- page :: errors
    "403.title" => "403 :: Access denied",
    "403.subtitle" => "You have no permissions for this page.",
    "403.message" => "The backend has different access levels.<br>You have no permissions for this page and the access is denied.",

    "404.title" => "404 :: Page not found",
    "404.subtitle" => "The requested page was not found.",
    "404.message" => "Check the url. Go back to the last page or start again from home page.",

    // ----- page :: users list
    "users.title" => "Users",
    "users.banner" => "List of users and their permissions",
    "users.info" => "The permissions are stored in config/settings.php.",
    "users.noacl" => "No ACL is configured yet. The backend is open with full admin access.",
    "users.global_admins" => "Global administrators",
    "users.app_permissions" => "App permissions",

    // ----- page :: object
    "object.welcome" => "Welcome!",
    "object.lets_create_first_item" => "Create your first item!",

    "object.tablecheck" => "DB table check",
    "object.tablecheck_update_required" => "The object configuration was changed. The database structure needs an update.",
    "object.tablecheck_edit_disabled" => "The [Edit] function was disabled. Fix the database problem and then reload the page.",
    "object.tablecheck_ok" => "OK",

    "object.mapped_to_relation" => "Mapped to value",
    "object.has_link_in_object" => "Hint: linked as lookup value in object",
    "object.inactive_has_a_link_already" => "Deactivated: it is linked already",
    "object.inactive_current_item" => "Deactivated: current item",

    "object.attachment_preview" => "Preview",
    "object.attachment-fix-app-config-in-attachments-key" => "No preview available.<br>Check the config in [app]/config/objects.php - section 'attachments'.<br>'baseurl' must be set and 'basedir' must point to an existing directory.",

    "object.add_relation_to" => "Add relation to",
    "object.relations_locked" => "The object was changed. That's why the relations were disabled. Save the object and edit it again, to change the relations.",

    "search.placeholder" => "Type to search ...",
    "search.hits" => "Hits",
    "search.results_for" => "Results für",
    "search.no_results_for" => "No hit for",
    "search.type_ahead" => "Type ahead ...",
    "search.hint_and_condition" => "Hint: There are a lot of results.<br>Use space to seperate multipe keywords that will be combined with logical AND.",
    "search.no_objects" => "No objects yet",
    "search.no_objects_hint" => "This application has no objects yet.<br>There is no data to search in.",

    // ----- page :: tools
    "tools.manage_backups" => "Manage backups",
    "tools.output" => "Output",

    // ----- debug box
    "debug.title" => "Debug information",
    "debug.queries" => "Database queries",
    "debug.processing_time" => "Processing time on server",
    "debug.max_memory" => "Max memory usage",
    "debug.records" => "Records",
    "debug.time_ms" => "Time [ms]",
    "debug.query" => "Query",
    "debug.error" => "Error",
    "debug.logs" => "Logs",
    "debug.log_level" => "Level",
    "debug.log_table" => "Table",
    "debug.log_method" => "Method",
    "debug.log_message" => "Messsage",
    
    "backup.file"=> "Backup file",
    "backup.timestamp"=> "Timestamp",
    "backup.size"=> "Size",

    "userprofile.banner" => "User profile",
    "userprofile.userid" => "User id",
    "userprofile.name" => "Shown name",
    "userprofile.groups" => "Groups",
    "userprofile.permissions" => "Permissions",
    "perms.view" => "View",
    "perms.edit" => "Edit",
    "perms.admin" => "Admin",
    "perms.none" => "No permissions were given.",

    // ----- simple words
    "abort"=> "Abort",
    "app"=>"Application",
    "attachments"=> "Attachments",
    "back"=>"Back",
    "backup"=>"Create new backup",
    "config"=>"Configuration",
    "confirm_delete"=>"Are you sure you want to delete this",
    "confirm_restore"=>"Are you sure you want to restore this",
    "created"=> "Created",
    "create_new_item" => "Create new entry",
    "delete"=>"Delete",
    "edit"=>"Edit",
    "error" => "ERROR",
    "files"=> "Files",
    "items"=> "Entries",
    "last_changed"=> "Last changed",
    "last_updated"=> "Last updated",
    "more"=>"More",
    "new"=>"New",
    "no_items_found"=>"No items found.",
    "optimize"=> "Optimize database",
    "properties"=> "Properties",
    "raw"=> "Raw",
    "readonly"=> "readonly",
    "relations"=> "Relations",
    "reload"=> "Reload",
    "reload_page"=> "Reload page",
    "repair" => "Repair",
    "restore" => "Restore",
    "save"=> "Save",
    "select_no_data_set"=>"No data in target table yet",
    "select_relation_item"=> "Select related item",
    "tdactions" => "Actions",
    "tools"=> "Tools",
    "view"=> "View",

    // ----- messages
    "msgerr.missing_permissions" => "ERROR: You do not have permissions.",

    "msgerr.no_or_invalid_object_name" => "No or an invalid object name was given.",
    "msgerr.no_or_invalid_object_name2" => "Please check the url or start again from HOME.",
    "msgerr.app_hint_missing"=>"[No key 'hint' in app config]",
    "msgerr.class_file_missing"=>"Class file does not exist",
    "msgerr.class_file_missing2"=>"The file was not found",
    "msgerr.class_not_initialized"=>"Unable to initialize class",
    "msgerr.class_not_initialized2"=>"Strange ... but the classfile was found",

    // ----- messages item

    "msgerr.wrong_itemdata"=> "ERROR: Invalid data - dataset won't be saved.",
    "msgerr.item_not_created"=> "ERROR: Unable save new item",
    "msgerr.item_not_updated"=> "ERROR: Item was not updated",
    "msgerr.item_not_deleted"=> "ERROR: Item was not deleted",

    "msgok.item_unchanged"            => "INFO: Item was not changed and wasn't saved again",
    "msgok.item_was_created"          => "OK: Created item id",
    "msgok.item_was_updated"          => "OK: Updated item",
    "msgok.item_was_deleted"          => "OK: Item was deleted",

    // ----- messages relation
    "msgerr.wrong_relationdata"=> "ERROR: Unable to create a relation - missing some data.",
    "msgerr.relation_was_not_created"=> "ERROR: relation was not created",
    "msgerr.relation_was_not_deleted"=> "ERROR: relation was not deleted",

    "msgok.relation_was_created"      => "OK: relation was created",
    "msgok.relation_was_deleted"      => "OK: relation was deleted",

    // ----- messages file upload
    "msgerr.fileupload_no_dir"=> "ERROR: Upload dir does not exist, upload forbidden",
    "msgerr.fileupload_wrong_id" => "ERROR: Upload handling was not started. Unable to get item #",

    "msgok.fileupload_object_created" => "OK: attachment object was created as",
    "msgok.fileupload_and_relation"   => "OK: Upload war erfolgreich und Relation wurde erstellt.",

    // ----- messages backup /restore
    "msgerr.action_not_implemented" => "ERROR: Action is not implmented yet",
    "msgerr.tools_backup_file_not_written" => "ERROR: Backup data could not be written as file.",
    "msgerr.tools_backup_no_data" => "ERROR: No backup data found.",
    "msgerr.tools_file_not_deleted" => "ERROR: File could not be deleted.",
    "msgerr.tools_file_not_deleted_not_found" => "ERROR: File could not be deleted. File not found.",
    "msgerr.tools_restore_failed" => "ERROR: Restore failed.",

    "msgok.tools_backup_created" => "OK: Backupwas created.",
    "msgok.tools_backup_restored" => "OK: Backup was restored.",
    "msgok.tools_file_deleted" => "OK: File was deleted.",

    // ----- messages save a config file
    "msgerr.file_was_not_saved" => "ERROR: File was not saved.",
    "msgerr.missing_filename" => "ERROR: Missing filename.",
    "msginfo.filecontent_is_the_same" => "SKIP: File content is unchanged. The file was not saved again.",
    "msgok.file_was_saved" => "OK: File was saved.",
    "msgerr.syntax_error" => "SYNTAX-ERROR was found. The file was not saved yet. Go back to fix the syntax.<br>Details:",

];