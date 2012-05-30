CREATE TABLE test (
	idemail  int(10) NOT NULL auto_increment,
	name varchar(150),
	PRIMARY KEY (idtest),
    UNIQUE KEY idtest (idtest),
    KEY idtest_2 (idtest)
)TYPE=MyISAM ;;
INSERT INTO email (name) VALUES ('This is a sample for package template');;