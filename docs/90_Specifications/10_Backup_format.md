## Backup format

When using the dump() method then a text file will be generated.

* Lines starting with `;` are comments

Each of other lines follows JSON syntax.

* `:meta:` - metadata of this backpup
* `tablename` table data for each table. The next key defines the information type
  * 1 x Create section
  * 1 x Insert section
  * N x multiple data lines per row
* `:done:` done section. If present then the backup is complete

**Example**:

```txt
{":meta:":{"timestamp":"2025-07-05 20:37:59","driver":"sqlite","tables":["objevents","pdo_db_relations","objeventtemplates","pdo_db_attachments","objcalendars"]}}
{"objevents":{"create":"CREATE TABLE objevents (    id INTEGER primary key autoincrement,\n    timecreated DATETIME,\n    timeupdated DATETIME,\n    deleted INTEGER,\n    label varchar(32),\n    starttime datetime,\n    endtime datetime,\n    calendar integer,\n    description varchar(2046))"}}
{"pdo_db_relations":{"create":"CREATE TABLE pdo_db_relations (    id INTEGER primary key autoincrement,\n    timecreated DATETIME,\n    timeupdated DATETIME,\n    deleted INTEGER,\n    from_table VARCHAR(32),\n    from_id INTEGER,\n    from_column VARCHAR(32),\n    to_table VARCHAR(32),\n    to_id INTEGER,\n    to_column VARCHAR(32),\n    uuid TEXT NOT NULL UNIQUE,\n    remark TEXT)"}}
{"objeventtemplates":{"create":"CREATE TABLE objeventtemplates (    id INTEGER primary key autoincrement,\n    timecreated DATETIME,\n    timeupdated DATETIME,\n    deleted INTEGER,\n    label varchar(64),\n    description varchar(2046))"}}
{"objeventtemplates":{"columns":["id","timecreated","timeupdated","deleted","label","description"]}}
{"objeventtemplates":{"data":[1,"2025-02-02 22:46:12",null,0,"Sysadmins - Wartungsfenster 1 Montag","<p>Am ersten Montag eines Montags werden veschiedene IML Server aktualisiert<br><\/p>"]}}
{"pdo_db_attachments":{"create":"CREATE TABLE pdo_db_attachments (    id INTEGER primary key autoincrement,\n    timecreated DATETIME,\n    timeupdated DATETIME,\n    deleted INTEGER,\n    label VARCHAR(255),\n    filename VARCHAR(255),\n    mime VARCHAR(32),\n    description VARCHAR(2048),\n    size INTEGER,\n    width INTEGER,\n    height INTEGER)"}}
{"objcalendars":{"create":"CREATE TABLE objcalendars (    id INTEGER primary key autoincrement,\n    timecreated DATETIME,\n    timeupdated DATETIME,\n    deleted INTEGER,\n    label varchar(32),\n    description varchar(4096),\n    color varchar(32))"}}
{"objcalendars":{"columns":["id","timecreated","timeupdated","deleted","label","description","color"]}}
{"objcalendars":{"data":[1,"2025-02-02 22:38:55","2025-02-03 22:38:58",0,"IML Sysadmins","","#1c71d8"]}}
{":done:":{"timestamp":"2025-07-05 20:37:59"}}
```
