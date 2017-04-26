/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     14/02/2013 03:13:00                          */
/*==============================================================*/


drop table if exists HistoriqueActionsAdmin;

drop table if exists HistoriqueActionsStagiaire;

drop table if exists ModulePreinscription;

drop table if exists SessionInscription;

drop table if exists Stagiaire;

drop table if exists SessionFormation;

drop table if exists ModuleFormation;

drop table if exists GroupModule;

drop table if exists Administrator;

/*==============================================================*/
/* Table: Administrator                                         */
/*==============================================================*/
create table Administrator
(
   a_id                 int(11) not null AUTO_INCREMENT,
   a_name               text not null,
   a_login              text not null,
   a_passwd             text not null,
   a_email              text not null,
   a_dtcrea             datetime not null,
   a_salt               text not null,
   primary key (a_id)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

/*==============================================================*/
/* Table: GroupModule                                           */
/*==============================================================*/
create table GroupModule
(
   gm_id                int(11) not null AUTO_INCREMENT,
   gm_name              text not null,
   primary key (gm_id)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

/*==============================================================*/
/* Table: ModuleFormation                                       */
/*==============================================================*/
create table ModuleFormation
(
   mf_id                int(11) not null AUTO_INCREMENT,
   gm_id                int(11),
   mf_code              varchar(254) not null,
   mf_intitule          text not null,
   mf_description       text not null,
   primary key (mf_id),
   unique key (mf_code)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

alter table ModuleFormation add constraint Niveau foreign key (gm_id)
      references GroupModule (gm_id) on delete cascade on update cascade;

/*==============================================================*/
/* Table: SessionFormation                                      */
/*==============================================================*/
create table SessionFormation
(
   sf_id                int(11) not null AUTO_INCREMENT,
   mf_id                int(11),
   sf_code              varchar(254)  not null,
   sf_intitule          text not null,
   sf_datedebut         date not null,
   sf_heuredebut        time not null,
   sf_lieu              text not null,
   sf_numcontactcentre  text not null,
   sf_conditionsreport  text not null,
   sf_dateinfo          text not null,
   sf_otherinfo         text not null,
   sf_maxparticipants   int(11) not null,
   sf_verouillage       boolean not null,
   sf_dtcrea            datetime not null,
   primary key (sf_id),
   unique key (sf_code)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

alter table SessionFormation add constraint DatesFormation foreign key (mf_id)
      references ModuleFormation (mf_id) on delete cascade on update cascade;

/*==============================================================*/
/* Table: Stagiaire                                             */
/*==============================================================*/
create table Stagiaire
(
   s_id                 int(11) not null AUTO_INCREMENT,
   s_nom                text not null,
   s_prenom             text not null,
   s_code               text not null,
   s_password           text not null,
   s_clearpassword           text not null,
   s_active             boolean not null default true,
   s_email              text not null,
   s_dtcrea             datetime not null,
   s_tel                text,
   s_nomcontact         text,
   s_emailcontact       text,
   s_salt               text not null,
   s_infosent           boolean not null default false,
   primary key (s_id)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

ALTER TABLE `Stagiaire` ADD `s_dtajout` DATETIME NOT NULL AFTER `s_email` ;
UPDATE Stagiaire AS s SET s.s_dtajout = DATE_ADD( s.s_dtcrea, INTERVAL -12 DAY ) WHERE s.s_dtajout = '0000-00-00 00:00:00'

/*==============================================================*/
/* Table: ModulePreinscription                                  */
/*==============================================================*/
create table ModulePreinscription
(
   mpi_id               int(11) not null AUTO_INCREMENT,
   s_id                 int(11) not null,
   mf_id                int(11) not null,
   mpi_dtcrea           datetime not null,
   primary key (mpi_id),
   unique key (s_id, mf_id)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

alter table ModulePreinscription add constraint PreinscriptionModule foreign key (mf_id)
      references ModuleFormation (mf_id) on delete cascade on update cascade;

alter table ModulePreinscription add constraint PreinscriptionStagiaire foreign key (s_id)
      references Stagiaire (s_id) on delete cascade on update cascade;

DELIMITER ;;
CREATE TRIGGER `ModulePreinscription_bi` BEFORE INSERT ON `ModulePreinscription` FOR EACH ROW
BEGIN
    SET NEW.mpi_dtcrea = NOW();
END;;
DELIMITER ;


/*==============================================================*/
/* Table: SessionInscription                                    */
/*==============================================================*/
create table SessionInscription
(
   si_id                int(11) not null AUTO_INCREMENT,
   s_id                 int(11) not null,
   sf_id                int(11) not null,
   si_dtcrea            datetime not null,
   primary key (si_id),
   unique key (s_id, sf_id)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

alter table SessionInscription add constraint InscriptionSession foreign key (sf_id)
      references SessionFormation (sf_id) on delete cascade on update cascade;

alter table SessionInscription add constraint InscriptionStagiaire foreign key (s_id)
      references Stagiaire (s_id) on delete cascade on update cascade;

ALTER TABLE `SessionInscription` ADD `si_convocation` BOOLEAN NOT NULL DEFAULT FALSE AFTER `si_dtcrea` ;

DELIMITER ;;
CREATE TRIGGER `SessionInscription_bi` BEFORE INSERT ON `SessionInscription` FOR EACH ROW
BEGIN
    SET NEW.si_dtcrea = NOW();
END;;
DELIMITER ;


/*==============================================================*/
/* Table: HistoriqueActionsStagiaire                            */
/*==============================================================*/
create table HistoriqueActionsStagiaire
(
   h_id                 int(11) not null AUTO_INCREMENT,
   s_id                 int(11),
   h_dtcrea             datetime not null,
   h_actiontype         text not null,
   h_actiondescription  text not null,
   primary key (h_id)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

alter table HistoriqueActionsStagiaire add constraint HAStagiaire foreign key (s_id)
      references Stagiaire (s_id) on delete cascade on update cascade;

/*==============================================================*/
/* Table: HistoriqueActionsAdmin                                */
/*==============================================================*/
create table HistoriqueActionsAdmin
(
   h_id                 int(11) not null AUTO_INCREMENT,
   a_id                 int(11),
   h_dtcrea             datetime not null,
   h_actiontype         text not null,
   h_actiondescription  text not null,
   primary key (h_id)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

alter table HistoriqueActionsAdmin add constraint HAAdmin foreign key (a_id)
      references Administrator (a_id) on delete cascade on update cascade;

INSERT INTO Administrator (a_name, a_login, a_passwd, a_email, a_dtcrea, a_salt) VALUES
('Salah Seif', 'seif', 'z3uQQ/jnfYaJ0FhXj8iDgXIjqbQRAWk1uEgxoJ52NRPY1o+Ld1WtkYpeqJEadu6s16a8omKAfE66NS5vRe5fEA==', 'seif.salah@gmail.com', NOW(), 'cfdd8f5eac962fbe272a4de7afb64589'),
('Léa Paolini', 'lea', 'TKrRQiNilqeKe/pKm7K30Pg/KO6Rkhc6v3agzS1W0DMfmfj+SzMNU78/WZLY6k8d02jW1B8LsFI6LBWCR2JLow==', 'lea.paolini@ilcfrance.com', NOW(), '62ec4c9c1df1198d02dc8825ae016bd8'),
('Fethi Regaya', 'fethi', 'TKrRQiNilqeKe/pKm7K30Pg/KO6Rkhc6v3agzS1W0DMfmfj+SzMNU78/WZLY6k8d02jW1B8LsFI6LBWCR2JLow==', 'fethi.regaya@ilcfrance.com', NOW(), '62ec4c9c1df1198d02dc8825ae016bd8');


INSERT INTO GroupModule (gm_name) VALUES
('Parcours de Formation d’anglais Niveau B1-­ (2-­2.4)'),
('Parcours de Formation d’anglais Niveau B1+ (2.5-­2.9)'),
('Parcours de Formation d’anglais Niveau B2 C1 (3-­4)');

INSERT INTO ModuleFormation (gm_id, mf_code, mf_intitule, mf_description) VALUES
(1, 'TE', "Techniques d'écoute - accents variés", 'Techniques d’Ecoute - Accents variées'),
(1, 'VP', 'Valoriser une présentation', 'Valoriser une présentation'),
(1, 'PTC', 'Participer à la téléconférence', 'Participer à la téléconférence'),
(1, 'GP', 'Gérer un projet', 'Gérer un projet'),
(1, 'CSP', 'Communication socio-professionnelle', 'Communication Socio-­Professionnelle'),
(1, 'RP', 'Rédaction professionnelle', 'Rédaction professionnelle'),
(1, 'LRS', 'Lecture et rédaction scientifique', 'Lecture et Rédaction scientifique'),
(1, 'SE', "Stratégies d'écoute - accents variés", 'Stratégies d’Ecoute - Accents variés'),
(2, 'SEI', 'Stratégies d’Ecoute - ­Inde', 'Stratégies d’Ecoute - ­Inde'),
(2, 'SPC', 'Persuader et convaincre', 'Persuader et convaincre'),
(2, 'GTC', 'Gérer la téléconférence', 'Gérer une Téléconférence'),
(2, 'MP', 'Mener un projet', 'Mener un projet'),
(2, 'NW', 'Networking', 'Networking'),
(2, 'RGD', 'Rédaction et Gestion de Documents', 'Rédaction et Gestion de Documents'),
(3, 'EIC', 'Effective International Communication', 'Effective International Communication'),
(3, 'IPS', 'International Public Speaking', 'International Public Speaking'),
(3, 'IPN', 'International Project Management', 'International Project Management'),
(3, 'ITW', 'International Teamwork', 'International Teamwork');


INSERT INTO SessionFormation (mf_id, sf_code, sf_intitule, sf_datedebut, sf_heuredebut, sf_lieu, sf_numcontactcentre, sf_conditionsreport, sf_dateinfo, sf_otherinfo, sf_maxparticipants, sf_verouillage, sf_dtcrea) VALUES
(1, 'TE-001',   'Session du 15 Mars 2013', '2013-03-15', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(2, 'VP-001',   'Session du 17 Mars 2013', '2013-03-17', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(3, 'PTC-001',  'Session du 19 Mars 2013', '2013-03-19', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(4, 'GP-001',   'Session du 21 Mars 2013', '2013-03-21', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(5, 'CSP-001',  'Session du 23 Mars 2013', '2013-03-23', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(6, 'RP-001',   'Session du 25 Mars 2013', '2013-03-25', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(7, 'LRS-001',  'Session du 27 Mars 2013', '2013-03-27', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(8, 'SE-001',   'Session du 29 Mars 2013', '2013-03-29', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(9, 'SEI-001',  'Session du 31 Mars 2013', '2013-03-31', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(10, 'SPC-001', 'Session du 2 Avril 2013', '2013-04-02', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(11, 'GTC-001', 'Session du 4 Avril 2013', '2013-04-04', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(12, 'MP-001',  'Session du 6 Avril 2013', '2013-04-06', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(13, 'NW-001',  'Session du 8 Avril 2013', '2013-04-08', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(14, 'RGD-001', 'Session du 10 Avril 2013', '2013-04-10', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(15, 'EIC-001', 'Session du 12 Avril 2013', '2013-04-12', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(16, 'IPS-001', 'Session du 14 Avril 2013', '2013-04-14', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(17, 'IPN-001', 'Session du 16 Avril 2013', '2013-04-16', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(18, 'ITW-001', 'Session du 18 Avril 2013', '2013-04-18', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),

(1, 'TE-002',   'Session du 15 Mai 2013', '2013-05-15', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(2, 'VP-002',   'Session du 17 Mai 2013', '2013-05-17', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(3, 'PTC-002',  'Session du 19 Mai 2013', '2013-05-19', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(4, 'GP-002',   'Session du 21 Mai 2013', '2013-05-21', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(5, 'CSP-002',  'Session du 23 Mai 2013', '2013-05-23', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(6, 'RP-002',   'Session du 25 Mai 2013', '2013-05-25', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(7, 'LRS-002',  'Session du 27 Mai 2013', '2013-05-27', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(8, 'SE-002',   'Session du 29 Mai 2013', '2013-05-29', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(9, 'SEI-002',  'Session du 31 Mai 2013', '2013-05-31', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(10, 'SPC-002', 'Session du 2 Juin 2013', '2013-06-02', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(11, 'GTC-002', 'Session du 4 Juin 2013', '2013-06-04', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(12, 'MP-002',  'Session du 6 Juin 2013', '2013-06-06', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(13, 'NW-002',  'Session du 8 Juin 2013', '2013-06-08', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(14, 'RGD-002', 'Session du 10 Juin 2013', '2013-06-10', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(15, 'EIC-002', 'Session du 12 Juin 2013', '2013-06-12', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(16, 'IPS-002', 'Session du 14 Juin 2013', '2013-06-14', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(17, 'IPN-002', 'Session du 16 Juin 2013', '2013-06-16', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(18, 'ITW-002', 'Session du 18 Juin 2013', '2013-06-18', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),

(1, 'TE-003',   'Session du 15 Juillet 2013', '2013-07-15', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(2, 'VP-003',   'Session du 17 Juillet 2013', '2013-07-17', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(3, 'PTC-003',  'Session du 19 Juillet 2013', '2013-07-19', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(4, 'GP-003',   'Session du 21 Juillet 2013', '2013-07-21', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(5, 'CSP-003',  'Session du 23 Juillet 2013', '2013-07-23', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(6, 'RP-003',   'Session du 25 Juillet 2013', '2013-07-25', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(7, 'LRS-003',  'Session du 27 Juillet 2013', '2013-07-27', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(8, 'SE-003',   'Session du 29 Juillet 2013', '2013-07-29', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(9, 'SEI-003',  'Session du 31 Juillet 2013', '2013-07-31', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(10, 'SPC-003', 'Session du 2 Aout 2013', '2013-08-02', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(11, 'GTC-003', 'Session du 4 Aout 2013', '2013-08-04', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(12, 'MP-003',  'Session du 6 Aout 2013', '2013-08-06', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(13, 'NW-003',  'Session du 8 Aout 2013', '2013-08-08', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(14, 'RGD-003', 'Session du 10 Aout 2013', '2013-08-10', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(15, 'EIC-003', 'Session du 12 Aout 2013', '2013-08-12', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(16, 'IPS-003', 'Session du 14 Aout 2013', '2013-08-14', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(17, 'IPN-003', 'Session du 16 Aout 2013', '2013-08-16', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(18, 'ITW-003', 'Session du 18 Aout 2013', '2013-08-18', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),

(1, 'TE-004',   'Session du 15 Septembre 2013', '2013-09-15', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(2, 'VP-004',   'Session du 17 Septembre 2013', '2013-09-17', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(3, 'PTC-004',  'Session du 19 Septembre 2013', '2013-09-19', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(4, 'GP-004',   'Session du 21 Septembre 2013', '2013-09-21', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(5, 'CSP-004',  'Session du 23 Septembre 2013', '2013-09-23', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(6, 'RP-004',   'Session du 25 Septembre 2013', '2013-09-25', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(7, 'LRS-004',  'Session du 27 Septembre 2013', '2013-09-27', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(8, 'SE-004',   'Session du 29 Septembre 2013', '2013-09-29', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(9, 'SEI-004',  'Session du 31 Septembre 2013', '2013-09-31', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(10, 'SPC-004', 'Session du 2 Octobre 2013', '2013-10-02', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(11, 'GTC-004', 'Session du 4 Octobre 2013', '2013-10-04', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(12, 'MP-004',  'Session du 6 Octobre 2013', '2013-10-06', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(13, 'NW-004',  'Session du 8 Octobre 2013', '2013-10-08', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(14, 'RGD-004', 'Session du 10 Octobre 2013', '2013-10-10', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 7h', 'une seule journée', 6, 0, NOW()),
(15, 'EIC-004', 'Session du 12 Octobre 2013', '2013-10-12', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(16, 'IPS-004', 'Session du 14 Octobre 2013', '2013-10-14', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(17, 'IPN-004', 'Session du 16 Octobre 2013', '2013-10-16', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW()),
(18, 'ITW-004', 'Session du 18 Octobre 2013', '2013-10-18', '09:00:00', 'Centre de Paris', '00331 123456789', 'Contacter le support pour plus d''informations', 'durée 14h', 'deux journées', 6, 0, NOW());
