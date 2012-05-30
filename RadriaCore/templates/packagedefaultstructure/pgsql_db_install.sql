CREATE TABLE "test" (
	"idtest" int4 DEFAULT nextval('email_idemail_seq'::text) NOT NULL,
	"name" character varying(150),
	PRIMARY KEY ("idtest")
);;
INSERT INTO "test" (name) VALUES ('test number 1');;