

ALTER TABLE `company`
    ADD INDEX iduser (iduser),
    DROP INDEX idcompany_2,
    DROP INDEX idcompany;

ALTER TABLE `contact_note`
    ADD INDEX idcontact (idcontact),
    ADD INDEX iduser (iduser),
    DROP INDEX idcontact_note_2,
    DROP INDEX idcontact_note;

ALTER TABLE `contact_website`
    MODIFY idcontact int(10) NULL  NULL,
    ADD INDEX idcontact (idcontact),
    DROP INDEX idcontact_website_2,
    DROP INDEX idcontact_website;
#
#  Fieldformat of
alter contact_website.idcontact changed from varchar(15) NOT NULL   to int(10) NULL  NULL
#  Possibly data modifications needed
#

ALTER TABLE `invoiceline`
    ADD INDEX idinvoice (idinvoice);

ALTER TABLE `message`
    ADD INDEX key_name (key_name),
    DROP INDEX idmessage_2,
    DROP INDEX idmessage;

ALTER TABLE `payment_invoice`
    ADD INDEX idpayment (idpayment),
    ADD INDEX idinvoice (idinvoice);

ALTER TABLE `project_discuss`
    ADD INDEX idproject_task (idproject_task),
    DROP INDEX idproject_discuss_2,
    DROP INDEX idproject_discuss;

ALTER TABLE `project_sharing`
    ADD INDEX idproject (idproject),
    ADD INDEX iduser (iduser),
    ADD INDEX idcoworker (idcoworker),
    DROP INDEX idproject_sharing_2,
    DROP INDEX idproject_sharing;

ALTER TABLE `project_task`
    ADD INDEX idtask (idtask),
    ADD INDEX idproject (idproject),
    DROP INDEX idproject_task_2,
    DROP INDEX idproject_task;

ALTER TABLE `recurrentinvoice`
    ADD INDEX idinvoice (idinvoice);

ALTER TABLE `task`
    ADD INDEX iduser (iduser),
    ADD INDEX idcontact (idcontact),
    DROP INDEX idtask_2,
    DROP INDEX idtask;

ALTER TABLE `task_category`
    ADD INDEX iduser (iduser),
    DROP INDEX idtask_category_2,
    DROP INDEX idtask_category;

ALTER TABLE `user_relations`
    ADD INDEX iduser (iduser),
    ADD INDEX idcoworker (idcoworker),
    ADD INDEX idcoworker_2 (idcoworker, accepted),
    DROP INDEX iduser_relations_2,
    DROP INDEX iduser_relations;




