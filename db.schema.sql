-- define character sets for connection between client and server
set character_set_client = 'utf8';
set character_set_results = 'utf8';


-- Clean start
drop database if exists ushort;
create database ushort;
use ushort;

drop table if exists urls;
create table urls (
	urls_id int unsigned not null auto_increment primary key,
	urls_long varchar(255) not null unique,
	urls_created_on timestamp not null,
	urls_creator char(15) not null,
	urls_referrals int unsigned not null default 0 key
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
