/*
alter table `catalogrule`
    ,add column `stop_rules_processing` tinyint (1) DEFAULT '1' NOT NULL
    ,ALTER COLUMN `from_date` `from_date` DATE ,
    ,MODIFY COLUMN `to_date` DATE ;
*/