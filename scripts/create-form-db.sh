#!/bin/bash
#
#	CREATING TABLES IN SQL DATABASE 
#
#	This script is created ONLY for the
#	Review Form Project.
#
#	Arguments:
#	----------
#	-h, --host	host (normaly 'localhost')
#	-u, --user	username
#	-d, --db	database name
#
#	-------------------------------------
#	by gellai.com
#	-------------------------------------
#
#	16 January 2019
#
#	

usage()
{
cat << EOF
usage: $0 options

This script is specially created for the Review Form article.
It will create 3 MySQL database tables and their fields. During
the process your password will be required. Please use the arguments
in the same order.

OPTIONS:
   -h      Host, the SQL database server's location. Default is '-h localhost'
   -u      Username to access the database
   -d      The name of the database which in the tables will be created
   
EXAMPLE:
	$0 -h localhost -u username -d database_name
   
EOF
}

HOST=
USER=
DB=
while getopts "h:u:d:v" OPTION
do
     case $OPTION in
         h)
             HOST=$OPTARG
             ;;
         u)
             USER=$OPTARG
             ;;
         d)
             DB=$OPTARG
             ;;
         v)
             VERBOSE=1
             ;;
         ?)
             usage
             exit
             ;;
     esac
done

if [[ -z $HOST ]] || [[ -z $USER ]] || [[ -z $DB ]]
then
     usage
     exit 1
fi

mysql -h $HOST -u $USER -p -e "
use $DB; 
DROP TABLE IF EXISTS srf_admin;
DROP TABLE IF EXISTS srf_review;

CREATE TABLE IF NOT EXISTS srf_admin (
    id          int(5) AUTO_INCREMENT,
    username    varchar(25) NOT NULL UNIQUE,
    password    varchar(250) NOT NULL,
    last_login  datetime NOT NULL,
    
    PRIMARY KEY (id)
);

LOCK TABLES srf_admin WRITE;
INSERT INTO srf_admin
VALUES (NULL, 'admin', MD5('demo1'), CURRENT_TIMESTAMP);

INSERT INTO srf_admin
VALUES (NULL, 'moderator', MD5('demo2'), CURRENT_TIMESTAMP);
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS srf_review (
    id              int(5) AUTO_INCREMENT,
    admin_id        int(5) DEFAULT NULL,
    name            varchar(50) NOT NULL,
    title           varchar(50) NOT NULL,
    email           varchar(50) NOT NULL,
    review          text NOT NULL,
    rating          enum('1','2','3','4','5') NOT NULL,
    status          enum('P','A','C') NOT NULL DEFAULT 'P',
    last_edit_date  datetime NOT NULL,
    create_date     datetime NOT NULL,
    is_deleted      enum('0', '1') NOT NULL DEFAULT '0',
    
    PRIMARY KEY (id),
    CONSTRAINT fk_admin_id FOREIGN KEY (admin_id) REFERENCES srf_admin(id)
);

LOCK TABLES srf_review WRITE;
INSERT INTO srf_review
VALUES (NULL, 2, 'r@b3rt', 'Another Test Review', 'robb@smail.com', 'Ea novum omittam ius, ne numquam sanctus vituperatoribus vel. Qui ne reque abhorreant, suas perfecto ullamcorper sea at. Vis eu qualisque abhorreant, nihil theophrastus eu vis. Ludus pertinacia no vim. Dicunt periculis an eam.<br /><br />Et posse adipisci nec, reprehendunt conclusionemque ad mel. Eu periculis dignissim qui. Veniam pertinax tincidunt id sed, ex dico oratio eum. Pro equidem appareat ut, et has agam fastidii. Te diam graecis has. Ea vim simul fabulas denique. Nec in vituperata percipitur concludaturque, pro velit falli cotidieque at, nibh fabulas ei quo.', '2', 'A', '2019-01-31 12:33:47', '2019-01-31 01:22:06', '0');

INSERT INTO srf_review
VALUES (NULL, 1, 'John_T', 'Good service', 'john_t@dmail.com', 'Lorem ipsum dolor sit amet, mei reque malis tempor ne, his et magna sadipscing, appareat singulis has no. Sed ut mucius dicunt noluisse, per no epicuri conclusionemque. Dolore soluta inermis his ei, ea legimus partiendo vis, quo ei dicat philosophia conclusionemque. Cum te dissentiet appellantur. Vix sint fastidii ut, minimum perfecto cu his. Ponderum nominati no mei.<br /><br />Senserit recteque cu sit. Omnes saperet ad ius. His ne libris utroque. Pri ei iriure evertitur. Omnium adipisci interpretaris ut quo, sea iisque veritus officiis an.<br /><br />Ne his possim scaevola democritum, sed inani melius reformidans eu. Ei vero epicuri petentium duo, ad augue nostrum has. Nam an wisi tantas noster. Has porro dicta everti ut, vim sint adipisci dissentias et, saepe dicant ad duo.', '4', 'A', '2019-01-31 12:12:43', '2019-01-31 01:22:06', '0');

INSERT INTO srf_review
VALUES (NULL, 1, 'Mike04587', 'Finibus Bonorum et Malorum', 'mike04587@fmail.com', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', '3', 'A', '2019-01-31 08:23:53', '2019-01-31 01:22:06', '1');

INSERT INTO srf_review
VALUES (NULL, NULL, 'Mr R', 'Sample Review', 'mrr413@gmail.com', 'The quick, brown fox jumps over a lazy dog. DJs flock by when MTV ax quiz prog. Junk MTV quiz graced by fox whelps. <br /><br />Bawds jog, flick quartz, vex nymphs. Waltz, bad nymph, for quick jigs vex! Fox nymphs grab quick-jived waltz. Brick quiz whangs jumpy veldt fox. Bright vixens jump; dozy fowl quack. Quick wafting zephyrs vex bold Jim. Quick zephyrs blow, vexing daft Jim. Sex-charged fop blew my junk TV quiz. How quickly daft jumping zebras vex. Two driven jocks help fax my big quiz. Quick, Baz, get my woven flax jodhpurs! \"Now fax quiz Jack!\" my brave ghost pled. Five quacking zephyrs jolt my wax bed. Flummoxed by job, kvetching W. zaps Iraq. <br />Cozy sphinx waves quart jug of bad milk. A very bad quack might jinx zippy fowls. Few quips galvanized the mock jury box. Quick brown dogs jump over the lazy fox. The jay, pig, fox, zebra, and my wolves quack! Blowzy red vixens fight for a quick jump. Joaquin Phoenix was gazed by MTV for luck.', '5', 'P', '2019-01-31 12:36:44', '2019-01-31 12:36:44', '0');
UNLOCK TABLES;
"
