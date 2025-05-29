## Overview

Remember the structure?!

```mermaid
flowchart TD
    OM(((fa:fa-gears Axels<br>Object manager))):::orange
    OM === A{{fa:fa-gear Config<br>Address book}}:::cfgfile
    OM === B{{fa:fa-gear Config<br>Blog}}:::cfgfile
    OM === C{{fa:fa-gear Config<br>Devices}}:::cfgfile

    subgraph " "
        A --- A1(fa:fa-file-code Person):::classes
        A --- A2(fa:fa-file-code Addresses):::classes
        A --- A3(fa:fa-file-code PhoneNumbers):::classes
    end

    subgraph " "
        B --- B1(fa:fa-file-code Articles):::classes
        B --- B3(fa:fa-file-code Tags):::classes
    end

    subgraph " "
        C --- C1(fa:fa-file-code Device):::classes
        C --- C2(fa:fa-file-code OS):::classes
        C --- C3(fa:fa-file-code Applications):::classes
    end

    classDef orange fill:#e71,stroke:#a61,color:#fff
    classDef cfgfile fill:#699,stroke:#144,color:#fff
    classDef classes fill:#469,stroke:#124,color:#fff
    classDef gray fill:#eee,stroke:#aaa

```

What we can (and or must) configure is:

* **1 configuration** for the ui
    * [admin/config/settings.php](10_settings.php.md) - Configure behaviour of the web ui and access rules for all apps<br><br>
* **Per application** there is its app folder. The foldername is the id of the application.

    * [apps/\<APPID\>/config/objects.php](20_application_config.md) - Configure an application here<br><br>

* In **each application configure its objects**

    * [apps/\<APPID\>/classes/\<OBJECT\>.class.php](30_Objects/_index.md) - Configure an object of an application<br><br>