
-- 
-- For the background accounting features
--
CREATE TABLE `ledger_account` (
    idledger_account int(10) NOT NULL auto_increment,
    type varchar(40) NOT NULL DEFAULT '',
    number int(10) NOT NULL DEFAULT '0',
    name varchar(70) NOT NULL DEFAULT '',
    idpaired int(10) NOT NULL DEFAULT '0',
    PRIMARY KEY (idledger_account)
);

CREATE TABLE `expense` (
    idexpense int(10) NOT NULL auto_increment,
    num int(10) NOT NULL DEFAULT '0',
    description varchar(200) NULL  NULL,
    date_receive date NULL  NULL,
    amount float NULL DEFAULT '0',
    taxes float NULL  NULL,
    date_paid date NULL DEFAULT '0000-00-00',
    type varchar(20) NULL  NULL,
    checknum varchar(25) NULL  NULL,
    idsuplier int(10) NULL  NULL,
    idcontact int(10) NOT NULL DEFAULT '0',
    idledger_account int(11) NULL  NULL,
    iduser int(10) NOT NULL DEFAULT '0',
    status varchar(10) NOT NULL DEFAULT '',
    PRIMARY KEY (idexpense),
    INDEX idcompany (idsuplier),
    INDEX idledger_account (idledger_account)
);

CREATE TABLE `expense_import` (
    idexpense_import int(10) NOT NULL auto_increment,
    category varchar(60) NOT NULL DEFAULT '',
    debit_date date NOT NULL DEFAULT '0000-00-00',
    description varchar(250) NOT NULL DEFAULT '',
    payment_method varchar(60) NOT NULL DEFAULT '',
    amount float(10,2) NOT NULL DEFAULT '0.00',
    iduser int(10) NULL  NULL,
    PRIMARY KEY (idexpense_import),
    UNIQUE idexpense_import_2 (idexpense_import),
    INDEX idexpense_import (idexpense_import)
);

CREATE TABLE `expense_suplier` (
    idexpense_suplier int(10) NOT NULL auto_increment,
    idcompany int(10) NULL  NULL,
    suplier_name varchar(60) NULL  NULL,
    PRIMARY KEY (idexpense_suplier)
);

