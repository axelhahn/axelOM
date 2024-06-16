## Installation

### Get the files

Use git clone to get the files OR extract the source.

### Prepare web

Set the webroot to the public_html folder.

OR

Copy the content below public_html folder into a new directory of a webroot.

### Create config

Create the file *public_html/admin/config/settings.php* by copying settings.php.dist.

### Create application(s)

Create the structure for an application in *public_html/apps/[myapp]/*

#### Folders

Create subfolders: classes, config, data and files.

#### Config

Create *config/objects.php* to define per application

* Metadata (label, icon)
* Database connection
* the objects of the application.

#### Object classes

In the classes folder create one class per object type.

#### Example structure

```txt
.
├── classes
│   ├── [object_1].class.php
│   └── [object_N].class.php
├── config
│   └── objects.php
├── data
└── files
```
