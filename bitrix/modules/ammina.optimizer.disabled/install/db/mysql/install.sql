CREATE TABLE IF NOT EXISTS `am_optimizer_history`
(
    `ID`           int(11) NOT NULL AUTO_INCREMENT,
    `PAGE_ID`      int(11)  DEFAULT NULL,
    `DATE_CHECK`   datetime DEFAULT NULL,
    `DESKTOP_DATA` longtext,
    `MOBILE_DATA`  longtext,
    PRIMARY KEY (`ID`),
    KEY `IX_PAGE_ID` (`PAGE_ID`),
    KEY `IX_DATE_CHECK` (`DATE_CHECK`)
);

CREATE TABLE IF NOT EXISTS `am_optimizer_page`
(
    `ID`                    int(11)      NOT NULL AUTO_INCREMENT,
    `PAGE_URL`              varchar(510) NOT NULL,
    `ACTIVE`                char(1)      NOT NULL DEFAULT 'Y',
    `STATUS`                char(1)      NOT NULL DEFAULT 'N',
    `DATE_CREATE`           datetime              DEFAULT NULL,
    `DATE_CHECK`            datetime              DEFAULT NULL,
    `DESKTOP_PERFORMANCE`   int(11)               DEFAULT NULL,
    `DESKTOP_ACCESSIBILITY` int(11)               DEFAULT NULL,
    `DESKTOP_BESTPRACTICES` int(11)               DEFAULT NULL,
    `DESKTOP_SEO`           int(11)               DEFAULT NULL,
    `DESKTOP_PWA`           int(11)               DEFAULT NULL,
    `MOBILE_PERFORMANCE`    int(11)               DEFAULT NULL,
    `MOBILE_ACCESSIBILITY`  int(11)               DEFAULT NULL,
    `MOBILE_BESTPRACTICES`  int(11)               DEFAULT NULL,
    `MOBILE_SEO`            int(11)               DEFAULT NULL,
    `MOBILE_PWA`            int(11)               DEFAULT NULL,
    `OLD_DATA`              text                  DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `IX_STATUS` (`STATUS`),
    KEY `ACTIVE` (`ACTIVE`)
);

CREATE TABLE IF NOT EXISTS `am_optimizer_settings`
(
    `ID`       int(11) NOT NULL AUTO_INCREMENT,
    `SITE_ID`  char(3)          DEFAULT NULL,
    `TYPE`     char(1) NOT NULL DEFAULT 'D',
    `SETTINGS` longtext,
    PRIMARY KEY (`ID`),
    KEY `IX_SITE_TYPE` (`SITE_ID`, `TYPE`) USING BTREE
);

CREATE TABLE IF NOT EXISTS `am_optimizer_files_opt`
(
    `ID`          int(11)      NOT NULL AUTO_INCREMENT,
    `ORIGINAL_ID` int(11) DEFAULT NULL,
    `FILE_NAME`   varchar(511) NOT NULL,
    `FILE_DATE`   datetime     NOT NULL,
    `FILE_SIZE`   bigint(20)   NOT NULL,
    PRIMARY KEY (`ID`),
    KEY `IX_ORIGINAL_ID` (`ORIGINAL_ID`),
    KEY `IX_FILE_NAME` (`FILE_NAME`(255)),
    KEY `IX_FILE_DATE` (`FILE_DATE`)
);

CREATE TABLE IF NOT EXISTS `am_optimizer_files_orig`
(
    `ID`             int(11)      NOT NULL AUTO_INCREMENT,
    `TYPE`           varchar(32) DEFAULT NULL,
    `FILE_NAME_HASH` varchar(4)  DEFAULT NULL,
    `FILE_NAME`      varchar(511) NOT NULL,
    `FILE_EXTENSION` varchar(32) DEFAULT NULL,
    `FILE_DATE`      datetime     NOT NULL,
    `FILE_SIZE`      bigint(20)  DEFAULT NULL,
    `CNT_OPTIMIZED`  int(11)     DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `IX_TYPE` (`TYPE`),
    KEY `IX_FILENAME_HASH` (`FILE_NAME_HASH`),
    KEY `IX_FILENAME` (`FILE_NAME`(255)),
    KEY `IX_CNT_OPTIMIZED` (`CNT_OPTIMIZED`),
    KEY `IX_FILE_EXTENSION` (`FILE_EXTENSION`),
    KEY `IX_FILE_DATE` (`FILE_DATE`) USING BTREE
);


CREATE TABLE IF NOT EXISTS `am_optimizer_stat_types`
(
    `ID`          int(11) NOT NULL AUTO_INCREMENT,
    `TYPE`        varchar(255) DEFAULT NULL,
    `TOTAL_COUNT` bigint(20)   DEFAULT NULL,
    `TOTAL_SIZE`  bigint(20)   DEFAULT NULL,
    PRIMARY KEY (`ID`)
);

