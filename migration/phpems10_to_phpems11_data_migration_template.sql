-- PHPEMS10 -> PHPEMS11 数据迁移 SQL 模板
-- 生成时间: 2026-03-26 20:50:36
-- 说明:
-- 1. 执行前请把 `phpems10_old` 与 `phpems11_new` 替换为你的实际数据库名
-- 2. 建议先完整备份旧库和新库
-- 3. 建议先在测试库执行，再上线
-- 4. 本模板仅处理 v10/v11 公共表的公共字段交集
-- 5. seminar / plugins 等非公共表见文档中的手工迁移说明

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ==================== 基础字典 ====================
-- x2_config
INSERT INTO `phpems11_new`.`x2_config` (`cfgapp`, `cfgsetting`)
SELECT `cfgapp`, `cfgsetting`
FROM `phpems10_old`.`x2_config`
ON DUPLICATE KEY UPDATE `cfgapp` = VALUES(`cfgapp`), `cfgsetting` = VALUES(`cfgsetting`);

-- x2_user_group
INSERT INTO `phpems11_new`.`x2_user_group` (`groupid`, `groupname`, `groupmoduleid`, `groupdescribe`, `groupright`, `groupmoduledefault`, `groupdefault`)
SELECT `groupid`, `groupname`, `groupmoduleid`, `groupdescribe`, `groupright`, `groupmoduledefault`, `groupdefault`
FROM `phpems10_old`.`x2_user_group`
ON DUPLICATE KEY UPDATE `groupid` = VALUES(`groupid`), `groupname` = VALUES(`groupname`), `groupmoduleid` = VALUES(`groupmoduleid`), `groupdescribe` = VALUES(`groupdescribe`), `groupright` = VALUES(`groupright`), `groupmoduledefault` = VALUES(`groupmoduledefault`), `groupdefault` = VALUES(`groupdefault`);

-- x2_subject
INSERT INTO `phpems11_new`.`x2_subject` (`subjectid`, `subject`, `subjectsetting`)
SELECT `subjectid`, `subject`, `subjectsetting`
FROM `phpems10_old`.`x2_subject`
ON DUPLICATE KEY UPDATE `subjectid` = VALUES(`subjectid`), `subject` = VALUES(`subject`), `subjectsetting` = VALUES(`subjectsetting`);

-- x2_sections
INSERT INTO `phpems11_new`.`x2_sections` (`sectionid`, `section`, `sectionsubjectid`, `sectiondescribe`, `sectionsequence`)
SELECT `sectionid`, `section`, `sectionsubjectid`, `sectiondescribe`, `sectionsequence`
FROM `phpems10_old`.`x2_sections`
ON DUPLICATE KEY UPDATE `sectionid` = VALUES(`sectionid`), `section` = VALUES(`section`), `sectionsubjectid` = VALUES(`sectionsubjectid`), `sectiondescribe` = VALUES(`sectiondescribe`), `sectionsequence` = VALUES(`sectionsequence`);

-- x2_knows
INSERT INTO `phpems11_new`.`x2_knows` (`knowsid`, `knows`, `knowssectionid`, `knowsdescribe`, `knowsstatus`, `knowssequence`, `knowsnumber`, `knowsquestions`)
SELECT `knowsid`, `knows`, `knowssectionid`, `knowsdescribe`, `knowsstatus`, `knowssequence`, `knowsnumber`, `knowsquestions`
FROM `phpems10_old`.`x2_knows`
ON DUPLICATE KEY UPDATE `knowsid` = VALUES(`knowsid`), `knows` = VALUES(`knows`), `knowssectionid` = VALUES(`knowssectionid`), `knowsdescribe` = VALUES(`knowsdescribe`), `knowsstatus` = VALUES(`knowsstatus`), `knowssequence` = VALUES(`knowssequence`), `knowsnumber` = VALUES(`knowsnumber`), `knowsquestions` = VALUES(`knowsquestions`);

-- x2_questype
INSERT INTO `phpems11_new`.`x2_questype` (`questid`, `questype`, `questsort`, `questchoice`)
SELECT `questid`, `questype`, `questsort`, `questchoice`
FROM `phpems10_old`.`x2_questype`
ON DUPLICATE KEY UPDATE `questid` = VALUES(`questid`), `questype` = VALUES(`questype`), `questsort` = VALUES(`questsort`), `questchoice` = VALUES(`questchoice`);

-- x2_area
INSERT INTO `phpems11_new`.`x2_area` (`areaid`, `area`, `areacode`, `arealevel`)
SELECT `areaid`, `area`, `areacode`, `arealevel`
FROM `phpems10_old`.`x2_area`
ON DUPLICATE KEY UPDATE `areaid` = VALUES(`areaid`), `area` = VALUES(`area`), `areacode` = VALUES(`areacode`), `arealevel` = VALUES(`arealevel`);

-- x2_province
INSERT INTO `phpems11_new`.`x2_province` (`id`, `provinceid`, `province`)
SELECT `id`, `provinceid`, `province`
FROM `phpems10_old`.`x2_province`
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `provinceid` = VALUES(`provinceid`), `province` = VALUES(`province`);

-- x2_city
INSERT INTO `phpems11_new`.`x2_city` (`id`, `cityid`, `city`, `father`)
SELECT `id`, `cityid`, `city`, `father`
FROM `phpems10_old`.`x2_city`
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `cityid` = VALUES(`cityid`), `city` = VALUES(`city`), `father` = VALUES(`father`);

-- x2_cityarea
INSERT INTO `phpems11_new`.`x2_cityarea` (`id`, `areaid`, `area`, `father`)
SELECT `id`, `areaid`, `area`, `father`
FROM `phpems10_old`.`x2_cityarea`
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`), `areaid` = VALUES(`areaid`), `area` = VALUES(`area`), `father` = VALUES(`father`);

-- ==================== 用户与内容 ====================
-- x2_user
INSERT INTO `phpems11_new`.`x2_user` (`userid`, `useropenid`, `userunionid`, `username`, `useremail`, `userpassword`, `usercoin`, `userregip`, `userregtime`, `userlogtime`, `userverifytime`, `usergroupid`, `usermoduleid`, `useranswer`, `manager_apps`, `usertruename`, `normal_favor`, `teacher_subjects`, `userprofile`, `usergender`, `userphone`, `useraddress`, `userphoto`, `userstatus`)
SELECT `userid`, `useropenid`, `userunionid`, `username`, `useremail`, `userpassword`, `usercoin`, `userregip`, `userregtime`, `userlogtime`, `userverifytime`, `usergroupid`, `usermoduleid`, `useranswer`, `manager_apps`, `usertruename`, `normal_favor`, `teacher_subjects`, `userprofile`, `usergender`, `userphone`, `useraddress`, `userphoto`, `userstatus`
FROM `phpems10_old`.`x2_user`
ON DUPLICATE KEY UPDATE `userid` = VALUES(`userid`), `useropenid` = VALUES(`useropenid`), `userunionid` = VALUES(`userunionid`), `username` = VALUES(`username`), `useremail` = VALUES(`useremail`), `userpassword` = VALUES(`userpassword`), `usercoin` = VALUES(`usercoin`), `userregip` = VALUES(`userregip`), `userregtime` = VALUES(`userregtime`), `userlogtime` = VALUES(`userlogtime`), `userverifytime` = VALUES(`userverifytime`), `usergroupid` = VALUES(`usergroupid`), `usermoduleid` = VALUES(`usermoduleid`), `useranswer` = VALUES(`useranswer`), `manager_apps` = VALUES(`manager_apps`), `usertruename` = VALUES(`usertruename`), `normal_favor` = VALUES(`normal_favor`), `teacher_subjects` = VALUES(`teacher_subjects`), `userprofile` = VALUES(`userprofile`), `usergender` = VALUES(`usergender`), `userphone` = VALUES(`userphone`), `useraddress` = VALUES(`useraddress`), `userphoto` = VALUES(`userphoto`), `userstatus` = VALUES(`userstatus`);

-- x2_content
INSERT INTO `phpems11_new`.`x2_content` (`contentid`, `contentcatid`, `contentmoduleid`, `contentuserid`, `contentusername`, `contentmodifier`, `contenttitle`, `contenttags`, `contentkeywords`, `contentthumb`, `contentlink`, `contentinputtime`, `contentmodifytime`, `contentsequence`, `contentdescribe`, `contentinfo`, `contentstatus`, `contenttemplate`, `contenttext`, `contentview`)
SELECT `contentid`, `contentcatid`, `contentmoduleid`, `contentuserid`, `contentusername`, `contentmodifier`, `contenttitle`, `contenttags`, `contentkeywords`, `contentthumb`, `contentlink`, `contentinputtime`, `contentmodifytime`, `contentsequence`, `contentdescribe`, `contentinfo`, `contentstatus`, `contenttemplate`, `contenttext`, `contentview`
FROM `phpems10_old`.`x2_content`
ON DUPLICATE KEY UPDATE `contentid` = VALUES(`contentid`), `contentcatid` = VALUES(`contentcatid`), `contentmoduleid` = VALUES(`contentmoduleid`), `contentuserid` = VALUES(`contentuserid`), `contentusername` = VALUES(`contentusername`), `contentmodifier` = VALUES(`contentmodifier`), `contenttitle` = VALUES(`contenttitle`), `contenttags` = VALUES(`contenttags`), `contentkeywords` = VALUES(`contentkeywords`), `contentthumb` = VALUES(`contentthumb`), `contentlink` = VALUES(`contentlink`), `contentinputtime` = VALUES(`contentinputtime`), `contentmodifytime` = VALUES(`contentmodifytime`), `contentsequence` = VALUES(`contentsequence`), `contentdescribe` = VALUES(`contentdescribe`), `contentinfo` = VALUES(`contentinfo`), `contentstatus` = VALUES(`contentstatus`), `contenttemplate` = VALUES(`contenttemplate`), `contenttext` = VALUES(`contenttext`), `contentview` = VALUES(`contentview`);

-- x2_category
INSERT INTO `phpems11_new`.`x2_category` (`catid`, `catapp`, `catlite`, `catname`, `catimg`, `caturl`, `catuseurl`, `catparent`, `catdes`, `cattpl`, `catmanager`, `catinmenu`, `catindex`, `catsubject`)
SELECT `catid`, `catapp`, `catlite`, `catname`, `catimg`, `caturl`, `catuseurl`, `catparent`, `catdes`, `cattpl`, `catmanager`, `catinmenu`, `catindex`, `catsubject`
FROM `phpems10_old`.`x2_category`
ON DUPLICATE KEY UPDATE `catid` = VALUES(`catid`), `catapp` = VALUES(`catapp`), `catlite` = VALUES(`catlite`), `catname` = VALUES(`catname`), `catimg` = VALUES(`catimg`), `caturl` = VALUES(`caturl`), `catuseurl` = VALUES(`catuseurl`), `catparent` = VALUES(`catparent`), `catdes` = VALUES(`catdes`), `cattpl` = VALUES(`cattpl`), `catmanager` = VALUES(`catmanager`), `catinmenu` = VALUES(`catinmenu`), `catindex` = VALUES(`catindex`), `catsubject` = VALUES(`catsubject`);

-- x2_navs
INSERT INTO `phpems11_new`.`x2_navs` (`navid`, `navtitle`, `navurl`, `navsequence`, `navstatus`)
SELECT `navid`, `navtitle`, `navurl`, `navsequence`, `navstatus`
FROM `phpems10_old`.`x2_navs`
ON DUPLICATE KEY UPDATE `navid` = VALUES(`navid`), `navtitle` = VALUES(`navtitle`), `navurl` = VALUES(`navurl`), `navsequence` = VALUES(`navsequence`), `navstatus` = VALUES(`navstatus`);

-- x2_docs
INSERT INTO `phpems11_new`.`x2_docs` (`docid`, `doctitle`, `docthumb`, `doccatid`, `dockeywords`, `doccontentid`, `docinputtime`, `docmodifytime`, `docsequence`, `docdescribe`, `doclocker`, `doclocktime`, `docneedmore`, `docsyslock`, `docistop`)
SELECT `docid`, `doctitle`, `docthumb`, `doccatid`, `dockeywords`, `doccontentid`, `docinputtime`, `docmodifytime`, `docsequence`, `docdescribe`, `doclocker`, `doclocktime`, `docneedmore`, `docsyslock`, `docistop`
FROM `phpems10_old`.`x2_docs`
ON DUPLICATE KEY UPDATE `docid` = VALUES(`docid`), `doctitle` = VALUES(`doctitle`), `docthumb` = VALUES(`docthumb`), `doccatid` = VALUES(`doccatid`), `dockeywords` = VALUES(`dockeywords`), `doccontentid` = VALUES(`doccontentid`), `docinputtime` = VALUES(`docinputtime`), `docmodifytime` = VALUES(`docmodifytime`), `docsequence` = VALUES(`docsequence`), `docdescribe` = VALUES(`docdescribe`), `doclocker` = VALUES(`doclocker`), `doclocktime` = VALUES(`doclocktime`), `docneedmore` = VALUES(`docneedmore`), `docsyslock` = VALUES(`docsyslock`), `docistop` = VALUES(`docistop`);

-- x2_docfloder
INSERT INTO `phpems11_new`.`x2_docfloder` (`dfid`, `dftitle`, `dfcatid`, `dfthumb`, `dftime`, `dfdecrbie`)
SELECT `dfid`, `dftitle`, `dfcatid`, `dfthumb`, `dftime`, `dfdecrbie`
FROM `phpems10_old`.`x2_docfloder`
ON DUPLICATE KEY UPDATE `dfid` = VALUES(`dfid`), `dftitle` = VALUES(`dftitle`), `dfcatid` = VALUES(`dfcatid`), `dfthumb` = VALUES(`dfthumb`), `dftime` = VALUES(`dftime`), `dfdecrbie` = VALUES(`dfdecrbie`);

-- x2_dochistory
INSERT INTO `phpems11_new`.`x2_dochistory` (`dhid`, `dhdocid`, `dhtitle`, `dhcontent`, `dhtime`, `dhusername`, `dhstatus`, `dhtop`)
SELECT `dhid`, `dhdocid`, `dhtitle`, `dhcontent`, `dhtime`, `dhusername`, `dhstatus`, `dhtop`
FROM `phpems10_old`.`x2_dochistory`
ON DUPLICATE KEY UPDATE `dhid` = VALUES(`dhid`), `dhdocid` = VALUES(`dhdocid`), `dhtitle` = VALUES(`dhtitle`), `dhcontent` = VALUES(`dhcontent`), `dhtime` = VALUES(`dhtime`), `dhusername` = VALUES(`dhusername`), `dhstatus` = VALUES(`dhstatus`), `dhtop` = VALUES(`dhtop`);

-- x2_course
INSERT INTO `phpems11_new`.`x2_course` (`courseid`, `coursetitle`, `coursemoduleid`, `coursecsid`, `coursethumb`, `courseuserid`, `courseinputtime`, `coursemodifytime`, `coursesequence`, `coursedescribe`, `course_files`, `course_oggfile`, `course_webmfile`, `course_youtu`, `pdf_file`, `coursepasstime`, `YPJX_YP`)
SELECT `courseid`, `coursetitle`, `coursemoduleid`, `coursecsid`, `coursethumb`, `courseuserid`, `courseinputtime`, `coursemodifytime`, `coursesequence`, `coursedescribe`, `course_files`, `course_oggfile`, `course_webmfile`, `course_youtu`, `pdf_file`, `coursepasstime`, `YPJX_YP`
FROM `phpems10_old`.`x2_course`
ON DUPLICATE KEY UPDATE `courseid` = VALUES(`courseid`), `coursetitle` = VALUES(`coursetitle`), `coursemoduleid` = VALUES(`coursemoduleid`), `coursecsid` = VALUES(`coursecsid`), `coursethumb` = VALUES(`coursethumb`), `courseuserid` = VALUES(`courseuserid`), `courseinputtime` = VALUES(`courseinputtime`), `coursemodifytime` = VALUES(`coursemodifytime`), `coursesequence` = VALUES(`coursesequence`), `coursedescribe` = VALUES(`coursedescribe`), `course_files` = VALUES(`course_files`), `course_oggfile` = VALUES(`course_oggfile`), `course_webmfile` = VALUES(`course_webmfile`), `course_youtu` = VALUES(`course_youtu`), `pdf_file` = VALUES(`pdf_file`), `coursepasstime` = VALUES(`coursepasstime`), `YPJX_YP` = VALUES(`YPJX_YP`);

-- x2_coursesubject
INSERT INTO `phpems11_new`.`x2_coursesubject` (`csid`, `cstitle`, `cscatid`, `csuserid`, `csbasicid`, `cssubjectid`, `cstime`, `csthumb`, `cssequence`, `csdescribe`, `csdemo`, `csprice`, `cstype`, `csprogress`)
SELECT `csid`, `cstitle`, `cscatid`, `csuserid`, `csbasicid`, `cssubjectid`, `cstime`, `csthumb`, `cssequence`, `csdescribe`, `csdemo`, `csprice`, `cstype`, `csprogress`
FROM `phpems10_old`.`x2_coursesubject`
ON DUPLICATE KEY UPDATE `csid` = VALUES(`csid`), `cstitle` = VALUES(`cstitle`), `cscatid` = VALUES(`cscatid`), `csuserid` = VALUES(`csuserid`), `csbasicid` = VALUES(`csbasicid`), `cssubjectid` = VALUES(`cssubjectid`), `cstime` = VALUES(`cstime`), `csthumb` = VALUES(`csthumb`), `cssequence` = VALUES(`cssequence`), `csdescribe` = VALUES(`csdescribe`), `csdemo` = VALUES(`csdemo`), `csprice` = VALUES(`csprice`), `cstype` = VALUES(`cstype`), `csprogress` = VALUES(`csprogress`);

-- ==================== 考试核心 ====================
-- x2_questions
INSERT INTO `phpems11_new`.`x2_questions` (`questionid`, `questiontype`, `question`, `questionuserid`, `questionusername`, `questionlastmodifyuser`, `questionselect`, `questionselectnumber`, `questionanswer`, `questiondescribe`, `questionknowsid`, `questioncreatetime`, `questionstatus`, `questionhtml`, `questionparent`, `questionsequence`, `questionlevel`, `questiondeler`, `questiondeltime`)
SELECT `questionid`, `questiontype`, `question`, `questionuserid`, `questionusername`, `questionlastmodifyuser`, `questionselect`, `questionselectnumber`, `questionanswer`, `questiondescribe`, `questionknowsid`, `questioncreatetime`, `questionstatus`, `questionhtml`, `questionparent`, `questionsequence`, `questionlevel`, `questiondeler`, `questiondeltime`
FROM `phpems10_old`.`x2_questions`
ON DUPLICATE KEY UPDATE `questionid` = VALUES(`questionid`), `questiontype` = VALUES(`questiontype`), `question` = VALUES(`question`), `questionuserid` = VALUES(`questionuserid`), `questionusername` = VALUES(`questionusername`), `questionlastmodifyuser` = VALUES(`questionlastmodifyuser`), `questionselect` = VALUES(`questionselect`), `questionselectnumber` = VALUES(`questionselectnumber`), `questionanswer` = VALUES(`questionanswer`), `questiondescribe` = VALUES(`questiondescribe`), `questionknowsid` = VALUES(`questionknowsid`), `questioncreatetime` = VALUES(`questioncreatetime`), `questionstatus` = VALUES(`questionstatus`), `questionhtml` = VALUES(`questionhtml`), `questionparent` = VALUES(`questionparent`), `questionsequence` = VALUES(`questionsequence`), `questionlevel` = VALUES(`questionlevel`), `questiondeler` = VALUES(`questiondeler`), `questiondeltime` = VALUES(`questiondeltime`);

-- x2_questionrows
INSERT INTO `phpems11_new`.`x2_questionrows` (`qrid`, `qrtype`, `qrquestion`, `qrknowsid`, `qrlevel`, `qrnumber`, `qruserid`, `qrusername`, `qrlastmodifyuser`, `qrtime`, `qrstatus`, `qrdeler`, `qrdeltime`)
SELECT `qrid`, `qrtype`, `qrquestion`, `qrknowsid`, `qrlevel`, `qrnumber`, `qruserid`, `qrusername`, `qrlastmodifyuser`, `qrtime`, `qrstatus`, `qrdeler`, `qrdeltime`
FROM `phpems10_old`.`x2_questionrows`
ON DUPLICATE KEY UPDATE `qrid` = VALUES(`qrid`), `qrtype` = VALUES(`qrtype`), `qrquestion` = VALUES(`qrquestion`), `qrknowsid` = VALUES(`qrknowsid`), `qrlevel` = VALUES(`qrlevel`), `qrnumber` = VALUES(`qrnumber`), `qruserid` = VALUES(`qruserid`), `qrusername` = VALUES(`qrusername`), `qrlastmodifyuser` = VALUES(`qrlastmodifyuser`), `qrtime` = VALUES(`qrtime`), `qrstatus` = VALUES(`qrstatus`), `qrdeler` = VALUES(`qrdeler`), `qrdeltime` = VALUES(`qrdeltime`);

-- x2_quest2knows
INSERT INTO `phpems11_new`.`x2_quest2knows` (`qkid`, `qkquestionid`, `qkknowsid`, `qktype`)
SELECT `qkid`, `qkquestionid`, `qkknowsid`, `qktype`
FROM `phpems10_old`.`x2_quest2knows`
ON DUPLICATE KEY UPDATE `qkid` = VALUES(`qkid`), `qkquestionid` = VALUES(`qkquestionid`), `qkknowsid` = VALUES(`qkknowsid`), `qktype` = VALUES(`qktype`);

-- x2_exams
INSERT INTO `phpems11_new`.`x2_exams` (`examid`, `examsubject`, `exam`, `examsetting`, `examquestions`, `examscore`, `examstatus`, `examtype`, `examauthorid`, `examauthor`, `examtime`, `examkeyword`, `examdecide`)
SELECT `examid`, `examsubject`, `exam`, `examsetting`, `examquestions`, `examscore`, `examstatus`, `examtype`, `examauthorid`, `examauthor`, `examtime`, `examkeyword`, `examdecide`
FROM `phpems10_old`.`x2_exams`
ON DUPLICATE KEY UPDATE `examid` = VALUES(`examid`), `examsubject` = VALUES(`examsubject`), `exam` = VALUES(`exam`), `examsetting` = VALUES(`examsetting`), `examquestions` = VALUES(`examquestions`), `examscore` = VALUES(`examscore`), `examstatus` = VALUES(`examstatus`), `examtype` = VALUES(`examtype`), `examauthorid` = VALUES(`examauthorid`), `examauthor` = VALUES(`examauthor`), `examtime` = VALUES(`examtime`), `examkeyword` = VALUES(`examkeyword`), `examdecide` = VALUES(`examdecide`);

-- x2_basic
INSERT INTO `phpems11_new`.`x2_basic` (`basicid`, `basic`, `basicareaid`, `basicsubjectid`, `basicsection`, `basicknows`, `basicexam`, `basicapi`, `basicdemo`, `basicthumb`, `basicprice`, `basicclosed`, `basictop`, `basicdescribe`)
SELECT `basicid`, `basic`, `basicareaid`, `basicsubjectid`, `basicsection`, `basicknows`, `basicexam`, `basicapi`, `basicdemo`, `basicthumb`, `basicprice`, `basicclosed`, `basictop`, `basicdescribe`
FROM `phpems10_old`.`x2_basic`
ON DUPLICATE KEY UPDATE `basicid` = VALUES(`basicid`), `basic` = VALUES(`basic`), `basicareaid` = VALUES(`basicareaid`), `basicsubjectid` = VALUES(`basicsubjectid`), `basicsection` = VALUES(`basicsection`), `basicknows` = VALUES(`basicknows`), `basicexam` = VALUES(`basicexam`), `basicapi` = VALUES(`basicapi`), `basicdemo` = VALUES(`basicdemo`), `basicthumb` = VALUES(`basicthumb`), `basicprice` = VALUES(`basicprice`), `basicclosed` = VALUES(`basicclosed`), `basictop` = VALUES(`basictop`), `basicdescribe` = VALUES(`basicdescribe`);

-- x2_openbasics
INSERT INTO `phpems11_new`.`x2_openbasics` (`obid`, `obuserid`, `obbasicid`, `obtime`, `obendtime`)
SELECT `obid`, `obuserid`, `obbasicid`, `obtime`, `obendtime`
FROM `phpems10_old`.`x2_openbasics`
ON DUPLICATE KEY UPDATE `obid` = VALUES(`obid`), `obuserid` = VALUES(`obuserid`), `obbasicid` = VALUES(`obbasicid`), `obtime` = VALUES(`obtime`), `obendtime` = VALUES(`obendtime`);

-- x2_opencourse
INSERT INTO `phpems11_new`.`x2_opencourse` (`ocid`, `ocuserid`, `occourseid`, `octime`, `ocendtime`)
SELECT `ocid`, `ocuserid`, `occourseid`, `octime`, `ocendtime`
FROM `phpems10_old`.`x2_opencourse`
ON DUPLICATE KEY UPDATE `ocid` = VALUES(`ocid`), `ocuserid` = VALUES(`ocuserid`), `occourseid` = VALUES(`occourseid`), `octime` = VALUES(`octime`), `ocendtime` = VALUES(`ocendtime`);

-- x2_questionanalysis
INSERT INTO `phpems11_new`.`x2_questionanalysis` (`qaid`, `qabasicid`, `qaquestionid`, `qauserid`, `qanumber`, `qarightnumber`, `qawrongnumber`, `qalasttime`, `qafirststatus`, `qalaststatus`, `qarate`, `qaqnparent`)
SELECT `qaid`, `qabasicid`, `qaquestionid`, `qauserid`, `qanumber`, `qarightnumber`, `qawrongnumber`, `qalasttime`, `qafirststatus`, `qalaststatus`, `qarate`, `qaqnparent`
FROM `phpems10_old`.`x2_questionanalysis`
ON DUPLICATE KEY UPDATE `qaid` = VALUES(`qaid`), `qabasicid` = VALUES(`qabasicid`), `qaquestionid` = VALUES(`qaquestionid`), `qauserid` = VALUES(`qauserid`), `qanumber` = VALUES(`qanumber`), `qarightnumber` = VALUES(`qarightnumber`), `qawrongnumber` = VALUES(`qawrongnumber`), `qalasttime` = VALUES(`qalasttime`), `qafirststatus` = VALUES(`qafirststatus`), `qalaststatus` = VALUES(`qalaststatus`), `qarate` = VALUES(`qarate`), `qaqnparent` = VALUES(`qaqnparent`);

-- ==================== 业务记录 ====================
-- x2_examhistory
INSERT INTO `phpems11_new`.`x2_examhistory` (`ehid`, `ehexamid`, `ehexam`, `ehtype`, `ehbasicid`, `ehquestion`, `ehsetting`, `ehscorelist`, `ehuseranswer`, `ehtime`, `ehscore`, `ehuserid`, `ehusername`, `ehstarttime`, `ehendtime`, `ehstatus`, `ehdecide`, `ehtimelist`, `ehopenid`, `ehneedresit`, `ehispass`, `ehteacher`, `ehdecidetime`, `ehbatch`)
SELECT `ehid`, `ehexamid`, `ehexam`, `ehtype`, `ehbasicid`, `ehquestion`, `ehsetting`, `ehscorelist`, `ehuseranswer`, `ehtime`, `ehscore`, `ehuserid`, `ehusername`, `ehstarttime`, `ehendtime`, `ehstatus`, `ehdecide`, `ehtimelist`, `ehopenid`, `ehneedresit`, `ehispass`, `ehteacher`, `ehdecidetime`, `ehbatch`
FROM `phpems10_old`.`x2_examhistory`
ON DUPLICATE KEY UPDATE `ehid` = VALUES(`ehid`), `ehexamid` = VALUES(`ehexamid`), `ehexam` = VALUES(`ehexam`), `ehtype` = VALUES(`ehtype`), `ehbasicid` = VALUES(`ehbasicid`), `ehquestion` = VALUES(`ehquestion`), `ehsetting` = VALUES(`ehsetting`), `ehscorelist` = VALUES(`ehscorelist`), `ehuseranswer` = VALUES(`ehuseranswer`), `ehtime` = VALUES(`ehtime`), `ehscore` = VALUES(`ehscore`), `ehuserid` = VALUES(`ehuserid`), `ehusername` = VALUES(`ehusername`), `ehstarttime` = VALUES(`ehstarttime`), `ehendtime` = VALUES(`ehendtime`), `ehstatus` = VALUES(`ehstatus`), `ehdecide` = VALUES(`ehdecide`), `ehtimelist` = VALUES(`ehtimelist`), `ehopenid` = VALUES(`ehopenid`), `ehneedresit` = VALUES(`ehneedresit`), `ehispass` = VALUES(`ehispass`), `ehteacher` = VALUES(`ehteacher`), `ehdecidetime` = VALUES(`ehdecidetime`), `ehbatch` = VALUES(`ehbatch`);

-- x2_favor
INSERT INTO `phpems11_new`.`x2_favor` (`favorid`, `favoruserid`, `favorsubjectid`, `favorquestionid`, `favortime`)
SELECT `favorid`, `favoruserid`, `favorsubjectid`, `favorquestionid`, `favortime`
FROM `phpems10_old`.`x2_favor`
ON DUPLICATE KEY UPDATE `favorid` = VALUES(`favorid`), `favoruserid` = VALUES(`favoruserid`), `favorsubjectid` = VALUES(`favorsubjectid`), `favorquestionid` = VALUES(`favorquestionid`), `favortime` = VALUES(`favortime`);

-- x2_record
INSERT INTO `phpems11_new`.`x2_record` (`recordid`, `recordquestionid`, `recorduserid`, `recordtime`, `recordsubjectid`)
SELECT `recordid`, `recordquestionid`, `recorduserid`, `recordtime`, `recordsubjectid`
FROM `phpems10_old`.`x2_record`
ON DUPLICATE KEY UPDATE `recordid` = VALUES(`recordid`), `recordquestionid` = VALUES(`recordquestionid`), `recorduserid` = VALUES(`recorduserid`), `recordtime` = VALUES(`recordtime`), `recordsubjectid` = VALUES(`recordsubjectid`);

-- x2_recorddata
INSERT INTO `phpems11_new`.`x2_recorddata` (`rdid`, `rduserid`, `rdsubjectid`, `rddata`, `rdtime`)
SELECT `rdid`, `rduserid`, `rdsubjectid`, `rddata`, `rdtime`
FROM `phpems10_old`.`x2_recorddata`
ON DUPLICATE KEY UPDATE `rdid` = VALUES(`rdid`), `rduserid` = VALUES(`rduserid`), `rdsubjectid` = VALUES(`rdsubjectid`), `rddata` = VALUES(`rddata`), `rdtime` = VALUES(`rdtime`);

-- x2_progress
INSERT INTO `phpems11_new`.`x2_progress` (`prsid`, `prsuserid`, `prstime`, `prsendtime`, `prscourseid`, `prscoursestatus`, `prsexamid`, `prsexamstatus`, `prstatus`)
SELECT `prsid`, `prsuserid`, `prstime`, `prsendtime`, `prscourseid`, `prscoursestatus`, `prsexamid`, `prsexamstatus`, `prstatus`
FROM `phpems10_old`.`x2_progress`
ON DUPLICATE KEY UPDATE `prsid` = VALUES(`prsid`), `prsuserid` = VALUES(`prsuserid`), `prstime` = VALUES(`prstime`), `prsendtime` = VALUES(`prsendtime`), `prscourseid` = VALUES(`prscourseid`), `prscoursestatus` = VALUES(`prscoursestatus`), `prsexamid` = VALUES(`prsexamid`), `prsexamstatus` = VALUES(`prsexamstatus`), `prstatus` = VALUES(`prstatus`);

-- x2_orders
INSERT INTO `phpems11_new`.`x2_orders` (`ordersn`, `ordertitle`, `orderdescribe`, `orderitems`, `orderprice`, `orderuserid`, `orderuserinfo`, `orderstatus`, `orderfullprice`, `ordercreatetime`, `orderpaytime`, `orderouttime`, `orderrecivetime`, `orderfaq`, `orderpost`, `orderapp`, `orderpaytype`, `orderbill`)
SELECT `ordersn`, `ordertitle`, `orderdescribe`, `orderitems`, `orderprice`, `orderuserid`, `orderuserinfo`, `orderstatus`, `orderfullprice`, `ordercreatetime`, `orderpaytime`, `orderouttime`, `orderrecivetime`, `orderfaq`, `orderpost`, `orderapp`, `orderpaytype`, `orderbill`
FROM `phpems10_old`.`x2_orders`
ON DUPLICATE KEY UPDATE `ordersn` = VALUES(`ordersn`), `ordertitle` = VALUES(`ordertitle`), `orderdescribe` = VALUES(`orderdescribe`), `orderitems` = VALUES(`orderitems`), `orderprice` = VALUES(`orderprice`), `orderuserid` = VALUES(`orderuserid`), `orderuserinfo` = VALUES(`orderuserinfo`), `orderstatus` = VALUES(`orderstatus`), `orderfullprice` = VALUES(`orderfullprice`), `ordercreatetime` = VALUES(`ordercreatetime`), `orderpaytime` = VALUES(`orderpaytime`), `orderouttime` = VALUES(`orderouttime`), `orderrecivetime` = VALUES(`orderrecivetime`), `orderfaq` = VALUES(`orderfaq`), `orderpost` = VALUES(`orderpost`), `orderapp` = VALUES(`orderapp`), `orderpaytype` = VALUES(`orderpaytype`), `orderbill` = VALUES(`orderbill`);

-- x2_coupon
INSERT INTO `phpems11_new`.`x2_coupon` (`couponsn`, `couponvalue`, `couponstatus`, `couponaddtime`, `couponendtime`, `couponusername`, `couponusetime`)
SELECT `couponsn`, `couponvalue`, `couponstatus`, `couponaddtime`, `couponendtime`, `couponusername`, `couponusetime`
FROM `phpems10_old`.`x2_coupon`
ON DUPLICATE KEY UPDATE `couponsn` = VALUES(`couponsn`), `couponvalue` = VALUES(`couponvalue`), `couponstatus` = VALUES(`couponstatus`), `couponaddtime` = VALUES(`couponaddtime`), `couponendtime` = VALUES(`couponendtime`), `couponusername` = VALUES(`couponusername`), `couponusetime` = VALUES(`couponusetime`);

-- x2_consumelog
INSERT INTO `phpems11_new`.`x2_consumelog` (`conlid`, `conlcost`, `conluserid`, `conlinfo`, `conltype`, `conltime`)
SELECT `conlid`, `conlcost`, `conluserid`, `conlinfo`, `conltype`, `conltime`
FROM `phpems10_old`.`x2_consumelog`
ON DUPLICATE KEY UPDATE `conlid` = VALUES(`conlid`), `conlcost` = VALUES(`conlcost`), `conluserid` = VALUES(`conluserid`), `conlinfo` = VALUES(`conlinfo`), `conltype` = VALUES(`conltype`), `conltime` = VALUES(`conltime`);

-- x2_feedback
INSERT INTO `phpems11_new`.`x2_feedback` (`fbid`, `fbquestionid`, `fbtype`, `fbcontent`, `fbuserid`, `fbtime`, `fbstatus`, `fbdoneuserid`, `fbdonetime`)
SELECT `fbid`, `fbquestionid`, `fbtype`, `fbcontent`, `fbuserid`, `fbtime`, `fbstatus`, `fbdoneuserid`, `fbdonetime`
FROM `phpems10_old`.`x2_feedback`
ON DUPLICATE KEY UPDATE `fbid` = VALUES(`fbid`), `fbquestionid` = VALUES(`fbquestionid`), `fbtype` = VALUES(`fbtype`), `fbcontent` = VALUES(`fbcontent`), `fbuserid` = VALUES(`fbuserid`), `fbtime` = VALUES(`fbtime`), `fbstatus` = VALUES(`fbstatus`), `fbdoneuserid` = VALUES(`fbdoneuserid`), `fbdonetime` = VALUES(`fbdonetime`);

-- x2_reply
INSERT INTO `phpems11_new`.`x2_reply` (`replyid`, `replyuserid`, `replyusername`, `replycommentid`, `replytime`, `replycontent`)
SELECT `replyid`, `replyuserid`, `replyusername`, `replycommentid`, `replytime`, `replycontent`
FROM `phpems10_old`.`x2_reply`
ON DUPLICATE KEY UPDATE `replyid` = VALUES(`replyid`), `replyuserid` = VALUES(`replyuserid`), `replyusername` = VALUES(`replyusername`), `replycommentid` = VALUES(`replycommentid`), `replytime` = VALUES(`replytime`), `replycontent` = VALUES(`replycontent`);

-- x2_comment
INSERT INTO `phpems11_new`.`x2_comment` (`cmtid`, `cmtopenid`, `cmtuserid`, `cmtreply`, `cmtcontent`, `cmttime`)
SELECT `cmtid`, `cmtopenid`, `cmtuserid`, `cmtreply`, `cmtcontent`, `cmttime`
FROM `phpems10_old`.`x2_comment`
ON DUPLICATE KEY UPDATE `cmtid` = VALUES(`cmtid`), `cmtopenid` = VALUES(`cmtopenid`), `cmtuserid` = VALUES(`cmtuserid`), `cmtreply` = VALUES(`cmtreply`), `cmtcontent` = VALUES(`cmtcontent`), `cmttime` = VALUES(`cmttime`);

-- x2_log
INSERT INTO `phpems11_new`.`x2_log` (`logid`, `loguserid`, `logcourseid`, `logtime`, `logstatus`, `logendtime`, `logprogress`)
SELECT `logid`, `loguserid`, `logcourseid`, `logtime`, `logstatus`, `logendtime`, `logprogress`
FROM `phpems10_old`.`x2_log`
ON DUPLICATE KEY UPDATE `logid` = VALUES(`logid`), `loguserid` = VALUES(`loguserid`), `logcourseid` = VALUES(`logcourseid`), `logtime` = VALUES(`logtime`), `logstatus` = VALUES(`logstatus`), `logendtime` = VALUES(`logendtime`), `logprogress` = VALUES(`logprogress`);

-- ==================== 系统与扩展 ====================
-- x2_answer
INSERT INTO `phpems11_new`.`x2_answer` (`asrid`, `asruserid`, `asraskid`, `asrcontent`, `asrtime`, `asrstatus`)
SELECT `asrid`, `asruserid`, `asraskid`, `asrcontent`, `asrtime`, `asrstatus`
FROM `phpems10_old`.`x2_answer`
ON DUPLICATE KEY UPDATE `asrid` = VALUES(`asrid`), `asruserid` = VALUES(`asruserid`), `asraskid` = VALUES(`asraskid`), `asrcontent` = VALUES(`asrcontent`), `asrtime` = VALUES(`asrtime`), `asrstatus` = VALUES(`asrstatus`);

-- x2_app
INSERT INTO `phpems11_new`.`x2_app` (`appid`, `appname`, `appthumb`, `appstatus`, `appsetting`)
SELECT `appid`, `appname`, `appthumb`, `appstatus`, `appsetting`
FROM `phpems10_old`.`x2_app`
ON DUPLICATE KEY UPDATE `appid` = VALUES(`appid`), `appname` = VALUES(`appname`), `appthumb` = VALUES(`appthumb`), `appstatus` = VALUES(`appstatus`), `appsetting` = VALUES(`appsetting`);

-- x2_ask
INSERT INTO `phpems11_new`.`x2_ask` (`askid`, `askuserid`, `asktitle`, `asktime`, `askcoin`, `askcontent`, `askisshow`, `askstatus`, `askorder`)
SELECT `askid`, `askuserid`, `asktitle`, `asktime`, `askcoin`, `askcontent`, `askisshow`, `askstatus`, `askorder`
FROM `phpems10_old`.`x2_ask`
ON DUPLICATE KEY UPDATE `askid` = VALUES(`askid`), `askuserid` = VALUES(`askuserid`), `asktitle` = VALUES(`asktitle`), `asktime` = VALUES(`asktime`), `askcoin` = VALUES(`askcoin`), `askcontent` = VALUES(`askcontent`), `askisshow` = VALUES(`askisshow`), `askstatus` = VALUES(`askstatus`), `askorder` = VALUES(`askorder`);

-- x2_attach
INSERT INTO `phpems11_new`.`x2_attach` (`attid`, `attpath`, `atttitle`, `attext`, `attinputtime`, `attsize`, `attmd5`, `attuserid`, `attcntype`)
SELECT `attid`, `attpath`, `atttitle`, `attext`, `attinputtime`, `attsize`, `attmd5`, `attuserid`, `attcntype`
FROM `phpems10_old`.`x2_attach`
ON DUPLICATE KEY UPDATE `attid` = VALUES(`attid`), `attpath` = VALUES(`attpath`), `atttitle` = VALUES(`atttitle`), `attext` = VALUES(`attext`), `attinputtime` = VALUES(`attinputtime`), `attsize` = VALUES(`attsize`), `attmd5` = VALUES(`attmd5`), `attuserid` = VALUES(`attuserid`), `attcntype` = VALUES(`attcntype`);

-- x2_attachtype
INSERT INTO `phpems11_new`.`x2_attachtype` (`atid`, `attachtype`, `attachexts`)
SELECT `atid`, `attachtype`, `attachexts`
FROM `phpems10_old`.`x2_attachtype`
ON DUPLICATE KEY UPDATE `atid` = VALUES(`atid`), `attachtype` = VALUES(`attachtype`), `attachexts` = VALUES(`attachexts`);

-- x2_block
INSERT INTO `phpems11_new`.`x2_block` (`blockid`, `block`, `blocktype`, `blockposition`, `blockcontent`)
SELECT `blockid`, `block`, `blocktype`, `blockposition`, `blockcontent`
FROM `phpems10_old`.`x2_block`
ON DUPLICATE KEY UPDATE `blockid` = VALUES(`blockid`), `block` = VALUES(`block`), `blocktype` = VALUES(`blocktype`), `blockposition` = VALUES(`blockposition`), `blockcontent` = VALUES(`blockcontent`);

-- x2_cequeue
INSERT INTO `phpems11_new`.`x2_cequeue` (`ceqid`, `ceqceid`, `cequserid`, `ceqinfo`, `ceqtime`, `ceqstatus`, `ceqordersn`, `ceqpubtime`)
SELECT `ceqid`, `ceqceid`, `cequserid`, `ceqinfo`, `ceqtime`, `ceqstatus`, `ceqordersn`, `ceqpubtime`
FROM `phpems10_old`.`x2_cequeue`
ON DUPLICATE KEY UPDATE `ceqid` = VALUES(`ceqid`), `ceqceid` = VALUES(`ceqceid`), `cequserid` = VALUES(`cequserid`), `ceqinfo` = VALUES(`ceqinfo`), `ceqtime` = VALUES(`ceqtime`), `ceqstatus` = VALUES(`ceqstatus`), `ceqordersn` = VALUES(`ceqordersn`), `ceqpubtime` = VALUES(`ceqpubtime`);

-- x2_certificate
INSERT INTO `phpems11_new`.`x2_certificate` (`ceid`, `cetitle`, `cethumb`, `ceprice`, `cebasic`, `cedays`, `cetime`, `cetpl`, `cetags`, `cedescribe`, `cetext`)
SELECT `ceid`, `cetitle`, `cethumb`, `ceprice`, `cebasic`, `cedays`, `cetime`, `cetpl`, `cetags`, `cedescribe`, `cetext`
FROM `phpems10_old`.`x2_certificate`
ON DUPLICATE KEY UPDATE `ceid` = VALUES(`ceid`), `cetitle` = VALUES(`cetitle`), `cethumb` = VALUES(`cethumb`), `ceprice` = VALUES(`ceprice`), `cebasic` = VALUES(`cebasic`), `cedays` = VALUES(`cedays`), `cetime` = VALUES(`cetime`), `cetpl` = VALUES(`cetpl`), `cetags` = VALUES(`cetags`), `cedescribe` = VALUES(`cedescribe`), `cetext` = VALUES(`cetext`);

-- x2_cnttouser
INSERT INTO `phpems11_new`.`x2_cnttouser` (`cturid`, `cturuserid`, `cturcontentid`, `cturtime`)
SELECT `cturid`, `cturuserid`, `cturcontentid`, `cturtime`
FROM `phpems10_old`.`x2_cnttouser`
ON DUPLICATE KEY UPDATE `cturid` = VALUES(`cturid`), `cturuserid` = VALUES(`cturuserid`), `cturcontentid` = VALUES(`cturcontentid`), `cturtime` = VALUES(`cturtime`);

-- x2_enroll
INSERT INTO `phpems11_new`.`x2_enroll` (`enrollid`, `enrollbatid`, `enrolluserid`, `enrolltruename`, `enrollpassport`, `enrollphone`, `enrolltime`, `enrollstatus`, `enrollordersn`, `enrollsign`, `enrollverify`, `enroll_address`)
SELECT `enrollid`, `enrollbatid`, `enrolluserid`, `enrolltruename`, `enrollpassport`, `enrollphone`, `enrolltime`, `enrollstatus`, `enrollordersn`, `enrollsign`, `enrollverify`, `enroll_address`
FROM `phpems10_old`.`x2_enroll`
ON DUPLICATE KEY UPDATE `enrollid` = VALUES(`enrollid`), `enrollbatid` = VALUES(`enrollbatid`), `enrolluserid` = VALUES(`enrolluserid`), `enrolltruename` = VALUES(`enrolltruename`), `enrollpassport` = VALUES(`enrollpassport`), `enrollphone` = VALUES(`enrollphone`), `enrolltime` = VALUES(`enrolltime`), `enrollstatus` = VALUES(`enrollstatus`), `enrollordersn` = VALUES(`enrollordersn`), `enrollsign` = VALUES(`enrollsign`), `enrollverify` = VALUES(`enrollverify`), `enroll_address` = VALUES(`enroll_address`);

-- x2_enroll_bats
INSERT INTO `phpems11_new`.`x2_enroll_bats` (`enbid`, `enbthumb`, `enbname`, `enbprice`, `enbmoduleid`, `enbstarttime`, `enbendtime`, `enbintro`, `enbtime`)
SELECT `enbid`, `enbthumb`, `enbname`, `enbprice`, `enbmoduleid`, `enbstarttime`, `enbendtime`, `enbintro`, `enbtime`
FROM `phpems10_old`.`x2_enroll_bats`
ON DUPLICATE KEY UPDATE `enbid` = VALUES(`enbid`), `enbthumb` = VALUES(`enbthumb`), `enbname` = VALUES(`enbname`), `enbprice` = VALUES(`enbprice`), `enbmoduleid` = VALUES(`enbmoduleid`), `enbstarttime` = VALUES(`enbstarttime`), `enbendtime` = VALUES(`enbendtime`), `enbintro` = VALUES(`enbintro`), `enbtime` = VALUES(`enbtime`);

-- x2_examsession
INSERT INTO `phpems11_new`.`x2_examsession` (`examsessionid`, `examsessionuserid`, `examsession`, `examsessionsetting`, `examsessionsign`, `examsessionbasic`, `examsessiontype`, `examsessionkey`, `examsessionquestion`, `examsessionuseranswer`, `examsessionstarttime`, `examsessiontime`, `examsessionstatus`, `examsessionscore`, `examsessionscorelist`, `examsessionissave`, `examsessiontimelist`, `examsessiontoken`)
SELECT `examsessionid`, `examsessionuserid`, `examsession`, `examsessionsetting`, `examsessionsign`, `examsessionbasic`, `examsessiontype`, `examsessionkey`, `examsessionquestion`, `examsessionuseranswer`, `examsessionstarttime`, `examsessiontime`, `examsessionstatus`, `examsessionscore`, `examsessionscorelist`, `examsessionissave`, `examsessiontimelist`, `examsessiontoken`
FROM `phpems10_old`.`x2_examsession`
ON DUPLICATE KEY UPDATE `examsessionid` = VALUES(`examsessionid`), `examsessionuserid` = VALUES(`examsessionuserid`), `examsession` = VALUES(`examsession`), `examsessionsetting` = VALUES(`examsessionsetting`), `examsessionsign` = VALUES(`examsessionsign`), `examsessionbasic` = VALUES(`examsessionbasic`), `examsessiontype` = VALUES(`examsessiontype`), `examsessionkey` = VALUES(`examsessionkey`), `examsessionquestion` = VALUES(`examsessionquestion`), `examsessionuseranswer` = VALUES(`examsessionuseranswer`), `examsessionstarttime` = VALUES(`examsessionstarttime`), `examsessiontime` = VALUES(`examsessiontime`), `examsessionstatus` = VALUES(`examsessionstatus`), `examsessionscore` = VALUES(`examsessionscore`), `examsessionscorelist` = VALUES(`examsessionscorelist`), `examsessionissave` = VALUES(`examsessionissave`), `examsessiontimelist` = VALUES(`examsessiontimelist`), `examsessiontoken` = VALUES(`examsessiontoken`);

-- x2_exercise
INSERT INTO `phpems11_new`.`x2_exercise` (`exerid`, `exeruserid`, `exerbasicid`, `exerknowsid`, `exernumber`, `exerqutype`)
SELECT `exerid`, `exeruserid`, `exerbasicid`, `exerknowsid`, `exernumber`, `exerqutype`
FROM `phpems10_old`.`x2_exercise`
ON DUPLICATE KEY UPDATE `exerid` = VALUES(`exerid`), `exeruserid` = VALUES(`exeruserid`), `exerbasicid` = VALUES(`exerbasicid`), `exerknowsid` = VALUES(`exerknowsid`), `exernumber` = VALUES(`exernumber`), `exerqutype` = VALUES(`exerqutype`);

-- x2_module
INSERT INTO `phpems11_new`.`x2_module` (`moduleid`, `modulecode`, `modulename`, `moduledescribe`, `moduleapp`, `moduletable`, `moduleallowreg`, `modulestatus`, `modulelockfields`, `modulebrands`)
SELECT `moduleid`, `modulecode`, `modulename`, `moduledescribe`, `moduleapp`, `moduletable`, `moduleallowreg`, `modulestatus`, `modulelockfields`, `modulebrands`
FROM `phpems10_old`.`x2_module`
ON DUPLICATE KEY UPDATE `moduleid` = VALUES(`moduleid`), `modulecode` = VALUES(`modulecode`), `modulename` = VALUES(`modulename`), `moduledescribe` = VALUES(`moduledescribe`), `moduleapp` = VALUES(`moduleapp`), `moduletable` = VALUES(`moduletable`), `moduleallowreg` = VALUES(`moduleallowreg`), `modulestatus` = VALUES(`modulestatus`), `modulelockfields` = VALUES(`modulelockfields`), `modulebrands` = VALUES(`modulebrands`);

-- x2_module_fields
INSERT INTO `phpems11_new`.`x2_module_fields` (`fieldid`, `fieldappid`, `fieldmoduleid`, `fieldsequence`, `field`, `fieldtitle`, `fieldlength`, `fielddescribe`, `fieldtype`, `fieldhtmltype`, `fieldhtmlproperty`, `fieldvalues`, `fielddefault`, `fieldlock`, `fieldindextype`, `fieldforbidactors`, `fieldsystem`, `fieldpublic`)
SELECT `fieldid`, `fieldappid`, `fieldmoduleid`, `fieldsequence`, `field`, `fieldtitle`, `fieldlength`, `fielddescribe`, `fieldtype`, `fieldhtmltype`, `fieldhtmlproperty`, `fieldvalues`, `fielddefault`, `fieldlock`, `fieldindextype`, `fieldforbidactors`, `fieldsystem`, `fieldpublic`
FROM `phpems10_old`.`x2_module_fields`
ON DUPLICATE KEY UPDATE `fieldid` = VALUES(`fieldid`), `fieldappid` = VALUES(`fieldappid`), `fieldmoduleid` = VALUES(`fieldmoduleid`), `fieldsequence` = VALUES(`fieldsequence`), `field` = VALUES(`field`), `fieldtitle` = VALUES(`fieldtitle`), `fieldlength` = VALUES(`fieldlength`), `fielddescribe` = VALUES(`fielddescribe`), `fieldtype` = VALUES(`fieldtype`), `fieldhtmltype` = VALUES(`fieldhtmltype`), `fieldhtmlproperty` = VALUES(`fieldhtmlproperty`), `fieldvalues` = VALUES(`fieldvalues`), `fielddefault` = VALUES(`fielddefault`), `fieldlock` = VALUES(`fieldlock`), `fieldindextype` = VALUES(`fieldindextype`), `fieldforbidactors` = VALUES(`fieldforbidactors`), `fieldsystem` = VALUES(`fieldsystem`), `fieldpublic` = VALUES(`fieldpublic`);

-- x2_poscontent
INSERT INTO `phpems11_new`.`x2_poscontent` (`pcid`, `pcposid`, `pcposapp`, `pccontentid`, `pcthumb`, `pcsequence`, `pctitle`, `pctime`, `pcdescribe`)
SELECT `pcid`, `pcposid`, `pcposapp`, `pccontentid`, `pcthumb`, `pcsequence`, `pctitle`, `pctime`, `pcdescribe`
FROM `phpems10_old`.`x2_poscontent`
ON DUPLICATE KEY UPDATE `pcid` = VALUES(`pcid`), `pcposid` = VALUES(`pcposid`), `pcposapp` = VALUES(`pcposapp`), `pccontentid` = VALUES(`pccontentid`), `pcthumb` = VALUES(`pcthumb`), `pcsequence` = VALUES(`pcsequence`), `pctitle` = VALUES(`pctitle`), `pctime` = VALUES(`pctime`), `pcdescribe` = VALUES(`pcdescribe`);

-- x2_position
INSERT INTO `phpems11_new`.`x2_position` (`posid`, `posname`, `posapp`)
SELECT `posid`, `posname`, `posapp`
FROM `phpems10_old`.`x2_position`
ON DUPLICATE KEY UPDATE `posid` = VALUES(`posid`), `posname` = VALUES(`posname`), `posapp` = VALUES(`posapp`);

-- x2_session
INSERT INTO `phpems11_new`.`x2_session` (`sessionid`, `sessionuserid`, `sessionusername`, `sessionpassword`, `sessionip`, `sessionmanage`, `sessiongroupid`, `sessioncurrent`, `sessionrandcode`, `sessionlogintime`, `sessiontimelimit`, `sessionlasttime`, `sessionmaster`)
SELECT `sessionid`, `sessionuserid`, `sessionusername`, `sessionpassword`, `sessionip`, `sessionmanage`, `sessiongroupid`, `sessioncurrent`, `sessionrandcode`, `sessionlogintime`, `sessiontimelimit`, `sessionlasttime`, `sessionmaster`
FROM `phpems10_old`.`x2_session`
ON DUPLICATE KEY UPDATE `sessionid` = VALUES(`sessionid`), `sessionuserid` = VALUES(`sessionuserid`), `sessionusername` = VALUES(`sessionusername`), `sessionpassword` = VALUES(`sessionpassword`), `sessionip` = VALUES(`sessionip`), `sessionmanage` = VALUES(`sessionmanage`), `sessiongroupid` = VALUES(`sessiongroupid`), `sessioncurrent` = VALUES(`sessioncurrent`), `sessionrandcode` = VALUES(`sessionrandcode`), `sessionlogintime` = VALUES(`sessionlogintime`), `sessiontimelimit` = VALUES(`sessiontimelimit`), `sessionlasttime` = VALUES(`sessionlasttime`), `sessionmaster` = VALUES(`sessionmaster`);

-- x2_survey
INSERT INTO `phpems11_new`.`x2_survey` (`svyid`, `svytitle`, `svythumb`, `svytime`, `svytype`, `svydescribe`, `svystime`, `svyendtime`, `svyuserid`)
SELECT `svyid`, `svytitle`, `svythumb`, `svytime`, `svytype`, `svydescribe`, `svystime`, `svyendtime`, `svyuserid`
FROM `phpems10_old`.`x2_survey`
ON DUPLICATE KEY UPDATE `svyid` = VALUES(`svyid`), `svytitle` = VALUES(`svytitle`), `svythumb` = VALUES(`svythumb`), `svytime` = VALUES(`svytime`), `svytype` = VALUES(`svytype`), `svydescribe` = VALUES(`svydescribe`), `svystime` = VALUES(`svystime`), `svyendtime` = VALUES(`svyendtime`), `svyuserid` = VALUES(`svyuserid`);

-- x2_survey_history
INSERT INTO `phpems11_new`.`x2_survey_history` (`syhyid`, `syhyuserid`, `syhysvyid`, `syhyanswers`, `syhycode`, `syhytime`)
SELECT `syhyid`, `syhyuserid`, `syhysvyid`, `syhyanswers`, `syhycode`, `syhytime`
FROM `phpems10_old`.`x2_survey_history`
ON DUPLICATE KEY UPDATE `syhyid` = VALUES(`syhyid`), `syhyuserid` = VALUES(`syhyuserid`), `syhysvyid` = VALUES(`syhysvyid`), `syhyanswers` = VALUES(`syhyanswers`), `syhycode` = VALUES(`syhycode`), `syhytime` = VALUES(`syhytime`);

-- x2_survey_node
INSERT INTO `phpems11_new`.`x2_survey_node` (`syneid`, `synesvyid`, `synetitle`, `synedescribe`)
SELECT `syneid`, `synesvyid`, `synetitle`, `synedescribe`
FROM `phpems10_old`.`x2_survey_node`
ON DUPLICATE KEY UPDATE `syneid` = VALUES(`syneid`), `synesvyid` = VALUES(`synesvyid`), `synetitle` = VALUES(`synetitle`), `synedescribe` = VALUES(`synedescribe`);

-- x2_survey_questions
INSERT INTO `phpems11_new`.`x2_survey_questions` (`syqnid`, `syqnsvyid`, `syqnsyneid`, `syqnquestion`, `syqnquestiontype`, `syqnquestionselect`, `syqnquestionselectnumber`)
SELECT `syqnid`, `syqnsvyid`, `syqnsyneid`, `syqnquestion`, `syqnquestiontype`, `syqnquestionselect`, `syqnquestionselectnumber`
FROM `phpems10_old`.`x2_survey_questions`
ON DUPLICATE KEY UPDATE `syqnid` = VALUES(`syqnid`), `syqnsvyid` = VALUES(`syqnsvyid`), `syqnsyneid` = VALUES(`syqnsyneid`), `syqnquestion` = VALUES(`syqnquestion`), `syqnquestiontype` = VALUES(`syqnquestiontype`), `syqnquestionselect` = VALUES(`syqnquestionselect`), `syqnquestionselectnumber` = VALUES(`syqnquestionselectnumber`);

-- x2_wxlogin
INSERT INTO `phpems11_new`.`x2_wxlogin` (`wxsid`, `wxinfo`, `wxtime`, `wxtoken`)
SELECT `wxsid`, `wxinfo`, `wxtime`, `wxtoken`
FROM `phpems10_old`.`x2_wxlogin`
ON DUPLICATE KEY UPDATE `wxsid` = VALUES(`wxsid`), `wxinfo` = VALUES(`wxinfo`), `wxtime` = VALUES(`wxtime`), `wxtoken` = VALUES(`wxtoken`);

-- ==================== 手工处理提示 ====================
-- x2_plugins: 不要从 v10 直接导，待 PHP11 插件安装后再补 pluginsetting
-- x2_seminar*: 待 `plugins/seminar` 建表后再导
-- deepseek_config.php: 不再保留根文件，改写入 x2_plugins.pluginsetting

SET FOREIGN_KEY_CHECKS = 1;
