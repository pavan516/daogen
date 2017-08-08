# DaoGen Changelog

## 0.5.6
* Chg: Changes to cache handling on insert/update/delete

## 0.5.5
+ New: Moved project to Github
* Fix: Added code to handle 'es' ending table names to change ending to just 's'.

## 0.5.4  
* Fix: Default values set to properties by Entity-Object now doefaults to NULL unless a defailt value was set in the SQL Create Table statement
* Fix: Controller generated entityObjects without the 'Entity' keyword at the end of classname
       
## 0.5.3
* Fix in Controllers generated $args[] retreival in GET and DELETE has an added "?? null;" to make args passing not fail with warnings
+ Added automatic Test Case generation for Entities 
* Controller classes no longer refer to any specific connection (was 'main')
* AbstractBaseDao::execCustomGetLastId() LastInsertId() only supported on MySql. Now Firebird,PostgreSql,Oracle,CockroachDb work with 'RETURNING id'; instead
* Dao classes now "return true;" instead of "return null;" when generated code returns error in handle*() methods
+ Renamed Entity objects to have Suffix "Entity" to be clearer what they are 
+ Makes Plural table names Singular. Support for "Properties" type of wors as well 
* BLOB fields and default PHP value of "". Now default value is ''
* ClassName was taken from TableName, and was lowercased. Now ucwords() it correctly 
+ Added if () {} with error logging if insert() or update() failes in Controller generation
         
## 0.5.2
* BugFix in handleGET() in controller generation
* Bugfix in Entity generator for DateTime and TimeZone handling

## 0.5.1
+ AbstractBaseDao new method fetchCount()
* Uses renamed \Nofuzz\Database\AbstractBaseDao as base for AbstractBaseDao
* Uses renamed \Nofuzz\Database\AbstractBaseEntity as base for AbstractBaseEntity
* Copies AbstractBaseDao and AbstractBaseEntity files to output

## 0.5.0
* Bugfix on cacheGetItemByField() in AbstractBaseDao
* Code cleanup / Comments Cleanup

## 0.4.9
+ DAO: fetchByKeywords() now includes ALL fields in table. Text fields use "<field> LIKE :<param>" the rest use "<field> = :<param>"
+ DAO: All fetchBy<fieldName>() methods are depricated/removed
+ DAO: New fetchBy() method added to AbstractBaseDao
+ Modifyed places that use fetchBy() statements to follow new method naming
+ Added commented lines with all set<FieldName> to handlePOST()

## 0.4.8
* Bugfixes

## 0.4.7
* Merge differances from 2 separate branches
* Bugfix Namespace in AbstractBaseDao and AbstractBaseEntity
* Bugfix in Dao generated classes for $this->cacheSetItem();

## 0.4.6
+ Entity class no longer contains "use ..." and expands namespace where needed
- Dao class no longer contains "use ..." for AbstractBaseDao and expands namespace where needed
+ Entity class now checks for NULL on dateTime/Timestamp fields in setXXX() method
* Bugfix on parsing table ddl
* Bugfix on creating Entity class (use removed)

## 0.4.5
+ Cache Methods to AbstractbaseDao
+ Cache calls for makeEntity(), fetchBy*()
+ Added AbstractbaseDao and AbstractBaseEntity classes in DaoGen dir
* Bugfixes to output structure (formatting)
