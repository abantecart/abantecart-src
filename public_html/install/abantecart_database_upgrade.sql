# fix of previous upgrade bug
alter table `ac_downloads` modify column `expire_days` int(11) null default null;