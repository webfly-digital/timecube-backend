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
    `FILE_NAME`      varchar(511) NOT NULL,
    `FILE_EXTENSION` varchar(32) DEFAULT NULL,
    `FILE_DATE`      datetime     NOT NULL,
    `FILE_SIZE`      bigint(20)  DEFAULT NULL,
    `CNT_OPTIMIZED`  int(11)     DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `IX_TYPE` (`TYPE`),
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
