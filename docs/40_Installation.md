## Installation

### Get the files

Use git clone to get the files 

```shell
cd ~/docker-projects/
git clone https://github.com/axelhahn/axelOM.git
```
This is the preferred method to keep it up to date with `git pull`

OR 

Download <https://github.com/axelhahn/axelOM/archive/refs/heads/main.zip> and extract it.

### Prepare web

Set the webroot to the public_html folder.

OR

Copy the content below public_html folder into a new directory of a webroot.

OR

If you have docker you can start the docker compose file.

### Create config

Create the file `public_html/admin/config/settings.php` by copying `settings.php.dist`.

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
