CREATE TABLE `ofuzdev`.`user_gitrepo` (
`iduser_gitrepo` INT( 50 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`iduser` INT( 50 ) NOT NULL ,
`git_repo` VARCHAR( 100 ) NOT NULL ,
`git_repourl` VARCHAR( 200 ) NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `ofuzdev`.`git_project` (
`idgit_project` INT( 12 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`iduser_gitrepo` INT( 12 ) NOT NULL ,
`idproject` INT( 12 ) NOT NULL
) ENGINE = MYISAM ;
