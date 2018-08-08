 
DATABASENAME="db_atisisbada_2017"
FILENAME=$DATABASENAME"_"$(date +"%Y%m%d")
DATABASEUSER="root"
DBPASSWORD="Adminpwa75"

#mysqldump -u $DATABASEUSER -p$DBPASSWORD --complete-insert --no-create-db --no-create-info --skip-events --skip-routines --skip-triggers $DATABASENAME > $FILENAME.data.sql
mysqldump -u $DATABASEUSER -p$DBPASSWORD -f --no-data --skip-events --skip-routines --skip-triggers $DATABASENAME > db_atisisbada_2017.struk.sql
mysqldump -u $DATABASEUSER -p$DBPASSWORD -f --routines --triggers --no-create-info --no-data --no-create-db --skip-opt  $DATABASENAME > db_atisisbada_2017.func.sql
