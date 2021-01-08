数据库表结构迁移  获取表结构migration文件
php artisan migrate:generate table1,table2,table3,table4,table5 //获取指定表
php artisan migrate:generate //获取所有表
执行数据库迁移
php artisan migrate
数据库基础数据填充seed 逆向操作获取seeder文件
php artisan iseed table_name1,table_name2....
数据填充
php artisan db:seed --class=seedfilename(注意：执行seed文件会强制覆盖原表所有数据，所以填充前确保seed文件数据是否完整) 

定时任务手动执行方法
php artisan + command文件中$signature 属性值（单独指定任务）

DB
host=192.168.1.225
db_name=support_report
db_user=support_report
db_passwd=U2FsdGVkX18JX1y44