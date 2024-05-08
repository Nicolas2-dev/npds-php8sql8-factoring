<?php

namespace Modules\TwoCore\Controllers\Admin\Database;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Savemysql extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = '';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'SavemySQL';

        $this->f_titre = __d('two_core', 'SavemySQL');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_', ''));
    }

/**
 * [PrepareString description]
 *
 * @param   string  $a_string|int  [$a_string description]
 *
 * @return  array
 */
function PrepareString(string|int $a_string = ''): string
{
    $search       = array('\\', '\'', "\x00", "\x0a", "\x0d", "\x1a"); //\x08\\x09, not required
    $replace      = array('\\\\', '\\\'', '\0', '\n', '\r', '\Z');

    return str_replace($search, $replace, (string) $a_string);
}

/**
 * [get_table_def description]
 *
 * @param   string  $table  [$table description]
 *
 * @return  string
 */
function get_table_def(string $table): string 
{
    global $crlf;

    settype($index, 'array');

    $k = 0;

    DB::setFetchMode(PDO::FETCH_NUM);

    DB::table($table)->select('*')->limit(1)->get();

    $count_line = DB::columnCount();

    $schema_create = '';
    $schema_create .= "DROP TABLE IF EXISTS `$table`;$crlf";
    $schema_create .= "CREATE TABLE $table ($crlf"; //
    
    foreach (DB::showFiledsTable($table) as $row) {

        $schema_create .= " ". $row['Field'] . " ". $row['Type'];

        if (isset($row['Default']) && (!empty($row['Default']) || $row['Default'] == "0")) {
            $schema_create .= " DEFAULT '". $row['Default'] . "'";
        }

        if ($row["Null"] != "YES") {
            $schema_create .= " NOT NULL";
        }

        if ($row["Extra"] != "") {
            $schema_create .= " ". $row['Extra'];
        }

        if ($k < ($count_line - 1)) {
            $schema_create .= ",$crlf";
        }

        $k++;
    }

    foreach (DB::showKeysTable($table) as $row) {
        $kname = $row['Key_name'];

        if (($kname != "PRIMARY") && ($row['Non_unique'] == 0)) {
            $kname = "UNIQUE|$kname";
        }

        if (!isset($index[$kname])) {
            $index[$kname] = array();
        }

        $index[$kname][] = $row['Column_name'];
    }

    foreach ($index as $x => $columns) {
        $schema_create .= ",$crlf";

        if ($x == "PRIMARY") {
            $schema_create .= " PRIMARY KEY (". implode(", ", $columns) . ")";
        } elseif (substr($x, 0, 6) == "UNIQUE") {
            $schema_create .= " UNIQUE ". substr($x, 7) . " (". implode(", ", $columns) . ")";
        } else {
            $schema_create .= " KEY $x (". implode(", ", $columns) . ")";
        }
    }

    $schema_create .= "$crlf)";
    $schema_create = stripslashes($schema_create);
    $schema_create .= " ENGINE=MyISAM DEFAULT CHARSET=". Config::get('database.default.charset') .";";

    return $schema_create;
}

/**
 * [get_table_content description]
 *
 * @param   string  $table  [$table description]
 *
 * @return  string
 */
function get_table_content(string $table): string|bool
{
    global $crlf;

    $schema_insert = '';

    DB::setFetchMode(PDO::FETCH_NUM);

    foreach(DB::table($table)->select('*')->get() as $row) {
        $schema_insert = "INSERT INTO $table VALUES (";

        $count_line = DB::columnCount();

        for ($j = 0; $j < $count_line; $j++) {
            if (!isset($row[$j])) {
                $schema_insert .= " NULL";
            } else {
                if ($row[$j] != "") {
                    $schema_insert .= " '". PrepareString($row[$j]) . "'";
                } else {
                    $schema_insert .= " ''";
                }

                if ($j < ($count_line - 1)) {
                    $schema_insert .= ",";
                }
            }
        }

        $schema_insert .= ");". $crlf;
    }

    if ($schema_insert != "") {
        $schema_insert = trim($schema_insert);
        
        return $schema_insert;
    }

    return false;
}

/**
 * [dbSave description]
 *
 * @return  void
 */
function dbSave(): void
{
    global $aid, $MSos, $crlf;

    $dbname = Config::get('database.default.database');

    @set_time_limit(600);

    $date_jour = date(__d('two_core', 'dateforop'));

    $date_op = date("mdy");
    $filename = $dbname . "-". $date_op;

    $tables = DB::list_tables();

    if ($tables == 0) {
        echo "&nbsp;". __d('two_core', 'Aucune table n\'a été trouvée') . "\n";
    } else {
        $heure_jour = date("H:i");
        
        $data = "# ========================================================$crlf"
            . "# $crlf"
            . "# ". __d('two_core', 'Sauvegarde de la base de données') . " : ". $dbname . " $crlf"
            . "# ". __d('two_core', 'Effectuée le') . " ". $date_jour . " : ". $heure_jour . " ". __d('two_core', 'par') . " ". $aid . " $crlf"
            . "# $crlf"
            . "# ========================================================$crlf";

        foreach ($tables as $table) {

            $data .= "$crlf"
                . "# --------------------------------------------------------$crlf"
                . "# $crlf"
                . "# ". __d('two_core', 'Structure de la table') . " '". $table . "' $crlf"
                . "# $crlf$crlf";

            $data .= get_table_def($table)
                . "$crlf$crlf"
                . "# $crlf"
                . "# ". __d('two_core', 'Contenu de la table') . " '". $table . "' $crlf"
                . "# $crlf$crlf";

            $data .= get_table_content($table)
                . "$crlf$crlf"
                . "# --------------------------------------------------------$crlf";
        }
    }

    send_file($data, $filename, "sql", $MSos);
}

/**
 * [dbSave_tofile description]
 *
 * @param   string  $repertoire      [$repertoire description]
 * @param   int     $linebyline      [$linebyline description]
 * @param   int     $savemysql_size  [$savemysql_size description]
 *
 * @return  void
 */
function dbSave_tofile(string $repertoire, int $linebyline = 0, int $savemysql_size = 256): void
{
    global $aid, $MSos, $crlf;

    @set_time_limit(600);

    $dbname = Config::get('database.default.database');

    $date_jour = date(__d('two_core', 'dateforop'));

    $date_op = date("ymd");
    
    $filename = $dbname . "-". $date_op;

    $tables = DB::list_tables();

    if ($tables == 0) {
        echo "&nbsp;". __d('two_core', 'Aucune table n\'a été trouvée') . "\n";
    } else {
        if ((!isset($repertoire)) or ($repertoire == "")) {
            $repertoire = ".";
        }

        if (!is_dir($repertoire)) {
            @umask(0000);
            @mkdir($repertoire, 0777);
            $fp = fopen($repertoire . "/index.html", 'w');
            fclose($fp);
        }

        $heure_jour = date("H:i");
        $data0 = "# ========================================================$crlf"
            . "# $crlf"
            . "# Sauvegarde de la base de données : ". $dbname . " $crlf"
            . "# Effectuée le ". $date_jour . " : ". $heure_jour . " par ". $aid . " $crlf"
            . "# $crlf"
            . "# ========================================================$crlf";
        $data1 = "";
        $ifile = 0;

        foreach ($tables as $table) {
            
            $data1 .= "$crlf"
                . "# --------------------------------------------------------$crlf"
                . "# $crlf"
                . "# Structure de la table '". $table . "' $crlf"
                . "# $crlf$crlf";

            $data1 .= get_table_def($table)
                . "$crlf$crlf"
                . "# $crlf"
                . "# Contenu de la table '". $table . "' $crlf"
                . "# $crlf$crlf";

            DB::setFetchMode(PDO::FETCH_NUM);

            foreach(DB::table($table)->select('*')->get() as $row) {
                $schema_insert = "INSERT INTO $table VALUES (";

                $count_line = DB::columnCount();

                for ($j = 0; $j < $count_line; $j++) {
                    if (!isset($row[$j])) {
                        $schema_insert .= " NULL";
                    } else {
                        if ($row[$j] != '') {
                            $schema_insert .= " '". PrepareString($row[$j]) . "'";
                        } else {
                            $schema_insert .= " ''";
                        }

                        if ($j < ($count_line - 1)) {
                            $schema_insert .= ",";
                        }
                    }
                }

                $schema_insert .= ");$crlf";

                $data1 .= $schema_insert;

                if ($linebyline == 1) {
                    if (strlen($data1) > ($savemysql_size*1024)) {
                        send_tofile($data0 . $data1, $repertoire, $filename . "-". sprintf("%03d", $ifile), "sql", $MSos);
                        $data1 = "";
                        $ifile++;
                    }
                }
            }

            $data1 .= "$crlf$crlf"
                . "# --------------------------------------------------------$crlf";

            if ($linebyline == 0) {
                if (strlen($data1) > ($savemysql_size * 1024)) {
                    send_tofile($data0 . $data1, $repertoire, $filename . "-". sprintf("%03d", $ifile), "sql", $MSos);
                    $data1 = "";
                    $ifile++;
                }
            }
        }

        if (strlen($data1) > 0) {
            send_tofile($data0 . $data1, $repertoire, $filename . "-". sprintf("%03d", $ifile), "sql", $MSos);
            $data1 = "";
            $ifile++;
        }
    }
}

switch ($op) {
    case "SavemySQL":
        $MSos = get_os();

        if ($MSos) {
            $crlf = "\r\n";
            $crlf2 = "\\r\\n";
        } else {
            $crlf = "\n";
            $crlf2 = "\\n";
        }

        $savemysql_mode = isset($savemysql_mode) ? $savemysql_mode : '';
        $savemysql_size = isset($savemysql_size) ? $savemysql_size : 256;

        if ($savemysql_mode == 2) {
            dbSave_tofile("slogs", 0, $savemysql_size);

            echo "<script type=\"text/javascript\">
                    //<![CDATA[
                    alert('". html_entity_decode(__d('two_core', 'Sauvegarde terminée. Les fichiers sont disponibles dans le répertoire /slogs'), ENT_COMPAT | ENT_HTML401, 'utf-8') . "');
                    //]]>
                    </script>";

            url::redirect_url("admin.php");
        } else if ($savemysql_mode == 3) {
            dbSave_tofile("slogs", 1, $savemysql_size);

            echo "<script type=\"text/javascript\">
                    //<![CDATA[
                    alert('". html_entity_decode(__d('two_core', 'Sauvegarde terminée. Les fichiers sont disponibles dans le répertoire /slogs'), ENT_COMPAT | ENT_HTML401, 'utf-8') . "');
                    //]]>
                    </script>";

            url::redirect_url("admin.php");
        } else {
            dbSave();

            url::redirect_url("admin.php");
        }
        break;

    default:
        header('Location: '. site_url('index.php'));
        break;



}