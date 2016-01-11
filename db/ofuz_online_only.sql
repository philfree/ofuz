-- 
-- Not sure this table is used any where:
--
CREATE TABLE `digittest` (
    iddigittest int(10) NOT NULL auto_increment,
    progress varchar(100) NOT NULL  ,
    PRIMARY KEY (iddigittest),
    UNIQUE iddigittest_2 (iddigittest),
    INDEX iddigittest (iddigittest)
);


CREATE TABLE `report_user_usage` (
    idreport_user_usage int(10) NOT NULL auto_increment,
    total_contacts int(15) NOT NULL DEFAULT '0',
    total_notes int(15) NOT NULL DEFAULT '0',
    total_projects int(15) NOT NULL DEFAULT '0',
    total_tasks int(15) NOT NULL DEFAULT '0',
    total_discussion int(15) NOT NULL DEFAULT '0',
    total_invoices int(15) NOT NULL DEFAULT '0',
    current_date date NOT NULL DEFAULT '0000-00-00',
    iduser int(15) NOT NULL  0,
    total_email_sent int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (idreport_user_usage)
);

CREATE TABLE `terms` (
    idterms int(10) NOT NULL auto_increment,
    term_desc varchar(200) NOT NULL  ,
    PRIMARY KEY (idterms),
    UNIQUE idterms_2 (idterms),
    INDEX idterms (idterms)
);

CREATE TABLE `user_plan` (
    iduser_plan int(10) NOT NULL auto_increment,
    plan varchar(10) NOT NULL  ,
    invoices varchar(20) NOT NULL  ,
    contacts varchar(20) NOT NULL  ,
    projects varchar(20) NOT NULL  ,
    emails varchar(20) NOT NULL  ,
    PRIMARY KEY (iduser_plan),
    UNIQUE iduser_plan_2 (iduser_plan),
    INDEX iduser_plan (iduser_plan)
);
