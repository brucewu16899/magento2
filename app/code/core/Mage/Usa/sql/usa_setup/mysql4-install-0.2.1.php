<?php

$this->startSetup();

$this->run(<<<EOT

DROP TABLE IF EXISTS `usa_postcode`;

CREATE TABLE `usa_postcode` (
  `country_id` smallint(6) unsigned NOT NULL default '0',
  `postcode` varchar(16) NOT NULL default '',
  `region_id` int(10) unsigned NOT NULL default '0',
  `county` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `postcode_class` char(1) NOT NULL default '',
  PRIMARY KEY  (`country_id`,`postcode`),
  KEY `country_id_2` (`country_id`,`region_id`),
  KEY `country_id_3` (`country_id`,`city`),
  KEY `country_id` (`country_id`),
  KEY `postcode` (`postcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT
);

/*
$fp = fopen($sqlFilesDir.DS.'us_zipcodes.txt', 'r');
while ($row = fgets($fp)) {
fclose($fp);
*/
foreach (file($sqlFilesDir.DS.'us_zipcodes.txt') as $row) {
	$this->run("insert into `usa_postcode` (country_id, postcode, region_id, county, city, postcode_class) values ".$row);
}

$this->run(<<<EOT

replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'carriers','Shipping Carriers','text','','','','',51,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'carriers/dhl','DHL','text','','','','',13,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/account','Account number','text','','','','',7,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/cutoff_cost','Cutoff cost','text','','','','',21,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/free_method','Free method','select','','','','usa/shipping_carrier_dhl_source_service',20,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/handling','Handling fee','text','','','','',10,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/id','Access ID','text','','','','',5,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/password','Password','text','','','','',6,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/shipment_type','Shipment type','select','','','','usa/shipping_carrier_dhl_source_shipmenttype',9,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/shipping_key','Shipping key','text','','','','',8,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/dhl/title','Title','text','','','','',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'carriers/fedex','FedEx','text','','','','',12,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/account','Account ID','text','','','','',3,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/cutoff_cost','Cutoff cost','text','','','','',21,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/dropoff','Dropoff','select','','','','usa/shipping_carrier_fedex_source_dropoff',5,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/free_method','Free method','select','','','','usa/shipping_carrier_fedex_source_method',20,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/handling','Handling fee','text','','','','',6,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/packaging','Packaging','select','','','','usa/shipping_carrier_fedex_source_packaging',4,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/fedex/title','Title','text','','','','',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'carriers/ups','UPS','text','','','','',10,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/container','Container','select','','','','usa/shipping_carrier_ups_source_container',3,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/cutoff_cost','Cutoff cost','text','','','','',21,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/dest_type','Destination type','select','','','','usa/shipping_carrier_ups_source_destType',4,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/free_method','Free method','select','','','','usa/shipping_carrier_ups_source_method',20,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/handling','Handling fee','text','','','','',5,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/pickup','Pickup method','select','','','','usa/shipping_carrier_ups_source_pickup',6,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/ups/title','Title','text','','','','',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (2,'carriers/usps','USPS','text','','','','',11,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/container','Container','select','','','','usa/shipping_carrier_usps_source_container',4,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/cutoff_cost','Cutoff cost','text','','','','',21,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/free_method','Free method','select','','','','usa/shipping_carrier_usps_source_service',20,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/handling','Handling fee','text','','','','',7,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/machinable','Machinable','select','','','','usa/shipping_carrier_usps_source_machinable',6,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/size','Size','select','','','','usa/shipping_carrier_usps_source_size',5,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/sort_order','Sort order','text','','','','',100,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/title','Title','text','','','','',2,1,1,1,'');
replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (3,'carriers/usps/userid','User ID','text','','','','',3,1,1,1,'');

replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/account','MAGENTO','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/active','1','0',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/cutoff_cost','','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/free_method','G','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/gateway_url','https://eCommerce.airborne.com/ApiLandingTest.asp','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/id','MAGENTO','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/model','usa/shipping_carrier_dhl','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/password','123123','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/shipment_type','P','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/sort_order','6','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/dhl/title','DHL','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/account','329311708','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/active','1','0',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/cutoff_cost','','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/dropoff','REGULARPICKUP','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/free_method','FEDEXGROUND','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/gateway_url','https://gateway.fedex.com/GatewayDC','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/model','usa/shipping_carrier_fedex','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/packaging','YOURPACKAGING','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/sort_order','5','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/fedex/title','Federal Express','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/active','1','0',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/container','CP','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/cutoff_cost','','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/dest_type','RES','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/free_method','GND','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/gateway_url','http://www.ups.com:80/using/services/rave/qcostcgi.cgi','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/handling','0','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/model','usa/shipping_carrier_ups','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/pickup','CC','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/sort_order','3','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/ups/title','United Parcel Service','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/usps/active','0','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/usps/cutoff_cost','','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/usps/free_method','PARCEL','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/usps/model','usa/shipping_carrier_usps','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/usps/sort_order','4','',0);
replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values ('default',0,'carriers/usps/userid','652VARIE8323','',0);

EOT
);

$this->endSetup();