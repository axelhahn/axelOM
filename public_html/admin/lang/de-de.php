<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * LANGUAGE FILE :: DEUTSCH :: DE-DE
 * 
 */
return [
    
    "id" => "Deutsch",

    "nav.home" => "Start",
    "nav.apps" => "Apps",
    "nav.about" => "&Uuml;ber ...",

    "nav.users" => "Benutzer",
    "nav.config" => "Konfiguration",

    // ----- page :: home
    "home.welcome" => "Willkommen bei <strong>Axels ObjManager</strong>",
    "home.banner" => "Backend zum Bearbeiten der Objekte verschiedener Applikationen.",
    "home.apps" => "Anzahl Applikationen",
    "home.objecttypes"=> "Anzahl Objekte",
    
    // ----- page :: app home

    "newapp.title" => "Neue Applikation erstellen",
    "newapp.appname" => "Verzeichnis (Kleinbuchstaben)",
    "newapp.appname-placeholder" => "z.B. appname",
    "newapp.icon" => "Icon CSS-Klasse",
    "newapp.label" => "z.B. Neuw Applikation",
    "newapp.label-placeholder" => "Name",
    "newapp.description" => "Beschreibung",

    // ----- page :: configuration
    "globalconfig.title" => "Globale Konfiguration",
    "globalconfig.banner" => "Bearbeite die globale Konfiguration dieses Backends: Sprache, User mit Rollen und mehr",

    // ----- page :: app config
    "config.title" => "Applikations-Konfiguration",
    "config.banner" => "Bearbeiten der Konfiguration der Applikation: Metadaten, Datenbank-Verbuindung, Objekte.",

    // ----- page :: about
    "about.title" => "&Uuml;ber dieses Backend",
    "about.text" => '
        Autor: <strong>Axel Hahn</strong><br>
        Programmiersprache: PHP 8<br>
        Lizenz: GNU GPL 3.0<br>
        Sourcecode: Github<br>
        Dokumentation: axel-hahn.de/docs/<br>
        <br>
        <strong>Verwendete Produkte</strong>:<br>
        Ohne die Einbindung anderer Produkte w&auml;re die Entwickklung viel aufw&auml;ndiger.<br>
        (M)Ein Dankeschön an die Entwickler der Tools
        <ul>
            <li><a href="https://adminlte.io/" target="_blank">AdminLTE</a> - Bootstrap Admin Template
                <ul>
                <li><a href="https://summernote.org/" target="_blank">Summernote</a> - Summmernote (HTML-Editor)</li>
                <li><a href="https://github.com/snapappointments/bootstrap-select" target="_blank">Bootstrap-Select Plugin</a></li>
                </ul>
            <li><a href="https://jquery.com/" target="_blank">jQuery</a></li>
            <li><a href="https://fontawesome.com/" target="_blank">Fontawesome</a> (Icons)</li>
            <li><a href="https://os-docs.iml.unibe.ch/adminlte-renderer/" target="_blank">AdminLTE-Renderer - Klasse</a> (Institut f&uuml;r Medizinische Lehre; Universit&auml;t Bern)</li>
            <li><a href="https://github.com/axelhahn/php-abstract-dbo" target="_blank">php-abstract-dbo </a> - Axels Abstract PDO Klasse (Axel Hahn)</li>
        </ul>
        ',

    // ----- page :: errors
    "403.title" => "403 :: Zugriff verweigert",
    "403.banner" => "Zugriff verweigert",
    "403.subtitle" => "Für diese Seite fehlt die Berechtigung.",
    "403.message" => "Das Backend kennt unterschiedliche Berechtigungsstufen.<br>F&uuml;r diese Seite fehlt die Berechtigung und deshalb ist der Zugriff verweigert.",

    "404.title" => "404 :: Seite nicht gefunden",
    "404.banner" => "404 :: Seite nicht gefunden",
    "404.subtitle" => "Die angeforderte Seite wurde nicht gefunden",
    "404.message" => "Pr&uuml;fe die URL. Der Parameter page=... ist falsch. Gehe zur letzten Seite zur&uuml;ck oder beginne von der Startseite.",

    "404-app.title" => "404 :: Seite nicht gefunden",
    "404-app.banner" => "Applikationsname ist falsch",
    "404-app.subtitle" => "Die angeforderte App wurde nicht gefunden",
    "404-app.message" => "Pr&uuml;fe die URL. Der Parameter app=... ist falsch. W&auml;hle in der Navigation oben eine existierende App. Oder gehe zur letzten Seite zur&uuml;ck oder beginne von der Startseite.",

    "404-obj.title" => "404 :: Seite nicht gefunden",
    "404-obj.banner" => "Object ist falsch",
    "404-obj.subtitle" => "Das angeforderte Objekt wurde nicht gefunden.",
    "404-obj.message" => "Pr&uuml;fe die URL. Der Parameter object=... ist falsch. W&auml;hle in der Navigation links ein existierendes Objekt. Oder gehe zur letzten Seite zur&uuml;ck oder beginne von der Startseite.",

    "404-obj-id.title" => "404 :: Seite nicht gefunden",
    "404-obj-id.banner" => "Object ID ist falsch",
    "404-obj-id.subtitle" => "Die angeforderte Objekt ID exisitiert nicht (mehr).",
    "404-obj-id.message" => "Pr&uuml;fe die URL. Der Parameter id=... ist falsch. W&auml;hle in der Navigation links das aktuelle den Objekttyp, um eine Liste von dessen Eintr&auml;gen zu sehen. Oder gehe zur letzten Seite zur&uuml;ck oder beginne von der Startseite.",

    // ----- page :: users list
    "users.title" => "Benutzer",
    "users.banner" => "Liste der Benutzer und Berechtigungen",
    "users.info" => "Die Berechtigungen sind in der config/settings.php gespeichert.",
    "users.noacl" => "Es ist keine ACL konfiguriert. Es gibt daher keine Berechtigungen.<br>Das Backend ist offen mit vollen Admin-Berechtigungen.",
    "users.global_admins" => "Globale Administratoren",
    "users.app_permissions" => "App-Berechtigungen",

    // ----- page :: object
    "object.welcome" => "Willkommen!",
    "object.lets_create_first_item" => "Lass uns einen allerersten Eintrag erstellen!",

    "object.tablecheck" => "DB Tabellen-Check",
    "object.tablecheck_update_required" => "Die Objekt-Konfiguration wurde geändert. DieDatenbank muss aktualisiert werden.",
    "object.tablecheck_edit_disabled" => "Die [Bearbeiten] Funktion wurde deaktiviert. Nach Korrektur der Datenbank bitte die Seite neu laden.",
    "object.tablecheck_ok" => "OK",

    "object.mapped_to_relation" => "Gemappt als Wert",
    "object.has_link_in_object" => "Hinweis: ist im Objekt als Lookup-Wert verlinkt",
    "object.inactive_has_a_link_already" => "Deaktiviert: es ist bereits verlinkt",
    "object.inactive_current_item" => "Deaktiviert: aktuelles Element",

    "object.attachment_preview" => "Vorschau",
    "object.attachment-fix-app-config-in-attachments-key" => "Vorschau ist nicht verfügbar.<br>Prüfen Sie die Config in [app]/config/objects.php - Sektion 'attachments'.<br>'baseurl' muss gesetzt sein und 'basedir' auf ein existierendes Verzeichnis zeigen.",
    "object.object_preview" => "Vorschau des Objekts",

    "object.add_relation_to" => "Relation hinzuf&uuml;gen zu",
    "object.relations_locked" => "Das Objekt wurde bearbeitet. Die Verknüpfungen sind daher gesperrt. Speichern Sie zunächst das Objekt und bearbeiten Sie es erneut, um die Verknüpfungen zu ändern.",

    "search.placeholder" => "Tippen zum Suchen ...",
    "search.hits" => "Treffer",
    "search.results_for" => "Ergebnisse für",
    "search.no_results_for" => "Kein Treffer für",
    "search.type_ahead" => "Bitte weiterschreiben ...",
    "search.hint_and_condition" => "Hinweis: Es gibt noch viele Treffer.<br>Mit Leerzeichen können mehrere Begriffe angegeben werden, die mit logischem UND verknüpft werden.",
    "search.no_objects" => "Keine Objekte",
    "search.no_objects_hint" => "Diese Applikation hat noch keine Objekte definiert.<br>Es existiert somit kein Datenbestand, der durchsucht werden könnte.",

    // ----- page :: tools
    "tools.manage_backups" => "Backups verwalten",
    "tools.output" => "Ausgabe",

    // ----- debug box
    "debug.title" => "Debug-Infos",
    "debug.queries" => "Datenbank-Abfragen",
    "debug.processing_time" => "Verarbeitungszeit am Server",
    "debug.max_memory" => "Max. RAM Verbrauch",
    "debug.records" => "Datensätze",
    "debug.time_ms" => "Zeit [ms]",
    "debug.query" => "Query",
    "debug.error" => "Fehler",
    "debug.logs" => "Logs",
    "debug.log_level" => "Level",
    "debug.log_table" => "Tabelle",
    "debug.log_method" => "Methode",
    "debug.log_message" => "Meldung",

    "backup.file"=> "Backup-Datei",
    "backup.timestamp"=> "Zeitstempel",
    "backup.size"=> "Dateigr&ouml;sse",

    "userprofile.banner" => "Benutzerprofil",
    "userprofile.userid" => "User-ID",
    "userprofile.name" => "Angezeigter Name",
    "userprofile.groups" => "Gruppen",
    "userprofile.permissions" => "Berechtigungen",
    "perms.view" => "Anzeigen",
    "perms.edit" => "Bearbeiten",
    "perms.admin" => "Admin",
    "perms.none" => "Es sind keine Berechtigungen zugeordnet.",

    // ----- simple words
    "abort"=> "Abbrechen",
    "attachments"=> "Anh&auml;nge",
    "app"=>"Application",
    "back"=>"Zur&uuml;ck",
    "backup"=>"Neues Backup erstellen",
    "config"=>"Konfiguration",
    "confirm_delete"=>"Sind Sie sicher, dass Sie dies L&ouml;schen m&ouml;chten",
    "confirm_restore"=> "Sind Sie sicher, dass Sie dies Wiederherstellen m&ouml;chten",
    "created"=> "Angelegt",
    "create_new_item" => "Neuen Eintrag anlegen",
    "delete"=>"L&ouml;schen",
    "edit"=>"Bearbeiten",
    "error" => "FEHLER",
    "files"=> "Dateien",
    "items"=> "Eintr&auml;ge",
    "last_changed"=> "Letzte &Auml;nderung",
    "last_updated"=> "Zuletzt ge&auml;ndert",
    "more"=>"Mehr",
    "new"=>"Neu",
    "no_items_found"=>"Keine Eintr&auml;ge gefunden.",
    "optimize"=> "Optimiere Datenbank",
    "properties"=> "Eigenschaften",
    "raw"=> "Raw",
    "readonly"=> "nur lesbar",
    "relations"=> "Verknüpfungen",
    "reload"=> "Neu laden",
    "reload_page"=> "Seite neu laden",
    "repair" => "Reparieren",
    "restore" => "Wiederherstellen",
    "save"=> "Speichern",
    "save_and_continue"=> "Zwischenspeichern",
    "select_no_data_set"=>"Noch keine Daten in Ziel-Tabelle",
    "select_relation_item"=> "Verkn&uuml;pfung w&auml;hlen",
    "td-actions"=> "Aktionen",
    "td-rel-target"=> "Ziel-Item",
    "td-rel-column"=> "Spalte",
    "td-rel-key"=> "Schlüssel",
    "tools"=> "Werkzeuge",
    "view"=> "Ansehen",

    // ----- messages
    "msgerr.missing_permissions" => "FEHLER: Sie haben nicht die ben&ouml;tigten Berechtigungen.",

    "msgerr.no_or_invalid_object_name" => "Es wurde kein oder ein ung&&uuml;ltiger Objektnamen angegeben.",
    "msgerr.no_or_invalid_object_name2" => "Pr&uuml;fen Sie den Objektnamen in der URL oder beginnen Sie von der Startseite.",
    "msgerr.app_hint_missing"=>"[Kein Key 'hint' in der App-Konfiguration gefunden]",
    "msgerr.class_file_missing"=>"Class-Datei existiert nicht.",
    "msgerr.class_file_missing2"=>"Die Datei wurde nicht gefunden",
    "msgerr.class_not_initialized"=>"Initialisierung der Klasse fehlgeschlagen",
    "msgerr.class_not_initialized2"=>"Komisch ... denn die PHP-Klassen-Datei wurde gefunden",

    // ----- messages new app
    "msgerr.missing_appname" => "FEHLER: Es wurde kein Verzeichnisname angegeben.",
    "msgerr.missing_label" => "FEHLER: Es wurde kein Name der neuen Applikation angegeben.",

    // ----- messages item
    "msgerr.wrong_itemdata"=> "FEHLER: Die Eingabedaten sind ung&uuml;ltig und werden nicht gespeichert",
    "msgerr.item_not_created"=> "FEHLER: Daten konnten nicht gespeichert werden",
    "msgerr.item_not_updated"=> "FEHLER: Der Eintrag konnte nicht aktualisiert werden",
    "msgerr.item_not_deleted"=> "FEHLER: Der Eintrag konnte nicht gel&ouml;scht werden",

    "msgok.item_unchanged"            => "INFO: Eintrag ist unver&auml;ndert und wurde nicht gespeichert",
    "msgok.item_was_created"          => "OK: Eintrag wurde angelegt mit ID",
    "msgok.item_was_updated"          => "OK: Eintrag wurde aktualisiert",
    "msgok.item_was_deleted"          => "OK: Eintrag wurde gel&ouml;scht",

    // ----- messages relations
    "msgerr.wrong_relationdata"=> "FEHLER: Die Relation konnte nicht erstellt werden. Es fehlen Daten.",
    "msgerr.relation_was_not_created"=> "FEHLER: Die Relation konnte nicht erstellt werden",
    "msgerr.relation_was_not_deleted"=> "FEHLER: Die Relation konnte nicht gel&ouml;scht werden",

    "msgerr.relation_not_allowed"     => "FEHLER: Die Relation zu diesem Objekt-Typ ist nicht erlaubt",

    "msgok.relation_was_created"      => "OK: Relation wude angelegt",
    "msgok.relation_was_deleted"      => "OK: Relation wurde gel&ouml;scht",

    // ----- messages file upload
    "msgerr.fileupload_no_dir"=> "FEHLER: Das Upload-Verzeichnis existiert nicht. Der Upload ist untersagt.",
    "msgerr.fileupload_wrong_id" => "FEHLER: Das Upload-Handling wurde nicht gestartet - die Objekt ID wurde nicht gefunden #",

    "msgok.fileupload_object_created" => "OK: Attachment-Object wurde angelegt als",
    "msgok.fileupload_and_relation"   => "OK: Der Upload war erfolgreich und die Relation wurde erstellt.",

    // ----- messages backup /restore
    "msgerr.action_not_implemented" => "FEHLER: Aktion wurde noch nicht implementiert",
    "msgerr.tools_backup_file_not_written" => "FEHLER: Backup-Daten konnte nicht als Datei geschrieben werden.",
    "msgerr.tools_backup_no_data" => "FEHLER: Keine Backup-Daten gefunden.",
    "msgerr.tools_file_not_deleted" => "FEHLER: Datei konnte nicht gel&ouml;scht werden.",
    "msgerr.tools_file_not_deleted_not_found" => "FEHLER: Datei konnte nicht gel&ouml;scht werden. Datei nicht gefunden.",
    "msgerr.tools_restore_failed" => "FEHLER: Backup konnte nicht wiederhergestellt werden.",

    "msgok.tools_backup_created" => "OK: Backup wurde angelegt",
    "msgok.tools_backup_restored" => "OK: Backup wurde wiederhergestellt.",
    "msgok.tools_file_deleted" => "OK: Datei wurde gel&ouml;scht.",

    // ----- messages save a config file
    "msgerr.file_was_not_saved" => "FEHLER: Die Datei konnte nicht gespeichert werden.",
    "msgerr.missing_filename" => "FEHLER: Der Dateiname fehlt. Daten k&ouml;nnen nicht gespeichert werden.",
    "msginfo.filecontent_is_the_same" => "SKIP: Der Inhalt ist unver&auml;ndert. Die wird nicht gespeichert.",
    "msgok.file_was_saved" => "OK: Die Datei wurde gespeichert.",

    "msgerr.syntax_error" => "SYNTAX-FEHLER gefunden. Die Datei wurde daher noch nicht gespeichert. Gehe zur&uuml;ck, um den Fehler zu korrigieren.<br>Details:",
];