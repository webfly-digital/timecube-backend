CREATE TABLE IF NOT EXISTS `am_optimizer_settings`
(
    `ID`       int(11) NOT NULL AUTO_INCREMENT,
    `SITE_ID`  char(3)          DEFAULT NULL,
    `TYPE`     char(1) NOT NULL DEFAULT 'D',
    `SETTINGS` longtext,
    PRIMARY KEY (`ID`),
    KEY `IX_SITE_TYPE` (`SITE_ID`, `TYPE`) USING BTREE
);
