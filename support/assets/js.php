<?php

declare(strict_types=1);

namespace npds\support\assets;

use npds\support\cache\cache;
use npds\support\utility\crypt;
use npds\system\support\facades\DB;

class js
{
  
    /**
     * fabrique un array js à partir de la requete sql et implente un auto complete pour l'input
     * (dependence : jquery.min.js ,jquery-ui.js) $nom_array_js=> nom du tableau javascript; 
     * $nom_champ=>nom de champ bd; $nom_tabl=>nom de table bd,$id_inpu=> id de l'input,
     * $temps_cache=>temps de cache de la requête. Si $id_inpu n'est pas défini retourne un array js.
     *
     * @param   string  $nom_array_js  [$nom_array_js description]
     * @param   string  $nom_champ     [$nom_champ description]
     * @param   string  $nom_tabl      [$nom_tabl description]
     * @param   string  $id_inpu       [$id_inpu description]
     * @param   int  $temps_cache   [$temps_cache description]
     *
     * @return  string                 [return description]
     */
    public static function auto_complete(string $nom_array_js, string $nom_champ, string $nom_tabl, string $id_inpu, int $temps_cache): string 
    {
        $list_json = '';
        $list_json .= 'var ' . $nom_array_js . ' = [';

        foreach (cache::Q_select3(DB::table($nom_tabl)->select($nom_champ)->get(), $temps_cache, crypt::encrypt('auto_complete')) as $ar_data) 
        {
            foreach ($ar_data as $champ) {
                if ($id_inpu == '') {
                    $list_json .= '"' . base64_encode($champ[$nom_champ]) . '",';
                } else {
                    $list_json .= '"' . $champ[$nom_champ] . '",';
                }
            }
        }

        $list_json = rtrim($list_json, ',');
        $list_json .= '];';
        $scri_js = '';
        
        if ($id_inpu == '') {
            $scri_js .= $list_json;
        } else {
            $scri_js .= '
        <script type="text/javascript">
        //<![CDATA[
        $(function() {
        ' . $list_json;
            if ($id_inpu != '')
                $scri_js .= '
        $( "#' . $id_inpu . '" ).autocomplete({
            source: ' . $nom_array_js . '
            });';
            $scri_js .= '
        });
        //]]>
        </script>';
        }

        return $scri_js;
    }
  
    /**
     * fabrique un pseudo array json à partir de la requete sql et implente un auto complete pour le champ input
     * (dependence : jquery-2.1.3.min.js ,jquery-ui.js)
     *
     * @param   string  $nom_array_js  [$nom_array_js description]
     * @param   string  $nom_champ     [$nom_champ description]
     * @param   string  $nom_tabl      [$nom_tabl description]
     * @param   string  $id_inpu       [$id_inpu description]
     * @param   string  $req           [$req description]
     *
     * @return  string                 [return description]
     */
    public static function auto_complete_multi(string $nom_array_js, string $nom_champ, string $nom_tabl, string $id_inpu, string $req): string
    {
        $list_json = '';
        $list_json .= $nom_array_js . ' = [';

        foreach (DB::table($nom_tabl)->select($nom_champ)->get() as $champ) 
        {
            $list_json .= '\'' . $champ[$nom_champ] . '\',';
        }

        $list_json = rtrim($list_json, ',');
        $list_json .= '];';
        $scri_js = '';
        $scri_js .= '
        <script type="text/javascript">
        //<![CDATA[
        var ' . $nom_array_js . ';
        $(function() {
        ' . $list_json . '
        function split( val ) {
        return val.split( /,\s*/ );
        }
        function extractLast( term ) {
        return split( term ).pop();
        }
        $( "#' . $id_inpu . '" )
        // dont navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).autocomplete( "instance" ).menu.active ) {
            event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: function( request, response ) {
            response( $.ui.autocomplete.filter(
                ' . $nom_array_js . ', extractLast( request.term ) ) );
            },
            focus: function() {
            return false;
            },
            select: function( event, ui ) {
            var terms = split( this.value );
            terms.pop();
            terms.push( ui.item.value );
            terms.push( "" );
            this.value = terms.join( ", " );
            return false;
            }
        });
        });
        //]]>
        </script>' . "\n";

        return $scri_js;
    }

    /**
     * [auto_complete_multièdb description]
     *
     * @param   string  $nom_array_js  [$nom_array_js description]
     * @param   string  $nom_champ     [$nom_champ description]
     * @param   string  $id_inpu       [$id_inpu description]
     * @param   array   $query           [$req description]
     *
     * @return  string
     */
    public static function auto_complete_multi_query(string $nom_array_js, string $nom_champ, string $id_inpu, array $query): string
    {
        $list_json = '';
        $list_json .= $nom_array_js . ' = [';
        
        foreach($query as $result) {
            $list_json .= '\'' . $result[$nom_champ] . '\',';
        }

        $list_json = rtrim($list_json, ',');
        $list_json .= '];';
        $scri_js = '';
        $scri_js .= '
        <script type="text/javascript">
        //<![CDATA[
        var '. $nom_array_js .';
        $(function() {
        '. $list_json .'
        function split( val ) {
        return val.split( /,\s*/ );
        }
        function extractLast( term ) {
        return split( term ).pop();
        }
        $( "#'. $id_inpu .'" )
        // dont navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).autocomplete( "instance" ).menu.active ) {
            event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: function( request, response ) {
            response( $.ui.autocomplete.filter(
                '. $nom_array_js .', extractLast( request.term ) ) );
            },
            focus: function() {
            return false;
            },
            select: function( event, ui ) {
            var terms = split( this.value );
            terms.pop();
            terms.push( ui.item.value );
            terms.push( "" );
            this.value = terms.join( ", " );
            return false;
            }
        });
        });
        //]]>
        </script>'. "\n";

        return $scri_js;
    }


}
