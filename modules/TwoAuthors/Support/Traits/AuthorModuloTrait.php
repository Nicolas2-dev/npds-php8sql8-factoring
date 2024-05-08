<?php

namespace Modules\TwoAuthors\Support\Traits;

use Two\Support\Facades\DB;


trait AuthorModuloTrait 
{

    /**
     * [listdroitsmodulo description]
     *
     * @return  array
     */
    function list_droits_modulo(): array 
    {
        $listdroits = '';
        $listdroitsmodulo = '';

        $R = DB::table('fonctions')->select('fid', 'fnom', 'fnom_affich', 'fcategorie')->where('finterface', 1)->where('fcategorie', '<', 7)->orderBy('fcategorie')->get();

        foreach($R as $func) {
            if ($func['fcategorie'] == 6) {
                $listdroitsmodulo .= '
                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                        <input class="ckbm form-check-input" id="ad_d_m_'. $func['fnom'] .'" type="checkbox" name="ad_d_m_'. $func['fnom'] .'" value="'. $func['fid'] .'" />
                        <label class="form-check-label" for="ad_d_m_'. $func['fnom'] .'">'. $func['fnom_affich'] .'</label>
                        </div>
                    </div>';
            } else {
                if ($func['fid'] != 12) {
                    $listdroits .= '
                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                        <input class="ckbf form-check-input" id="ad_d_'. $func['fid'] .'" type="checkbox" name="ad_d_'. $func['fid'] .'" value="'. $func['fid'] .'" />
                        <label class="form-check-label" for="ad_d_'. $func['fid'] .'">'. $func['fnom_affich'] .'</label>
                        </div>
                    </div>';
                }
            }
        }

        return array($listdroitsmodulo, $listdroits);
    }

    /**
     * [scri_check description]
     *
     * @return  string
     */
    function script_check(): string
    {
        return '
            <script type="text/javascript">
            //<![CDATA[
            $(function () {
                check = $("#cb_radminsuper").is(":checked");
                if(check) {
                    $("#adm_droi_f, #adm_droi_m").addClass("collapse");
                }
            });
            $("#cb_radminsuper").on("click", function(){
                check = $("#cb_radminsuper").is(":checked");
                if(check) {
                    $("#adm_droi_f, #adm_droi_m").toggleClass("collapse","collapse show");
                    $(".ckbf, .ckbm, #ckball_f, #ckball_m").prop("checked", false);
                } else {
                    $("#adm_droi_f, #adm_droi_m").toggleClass("collapse","collapse show");
                }
            }); 
            $(document).ready(function(){ 
                $("#ckball_f").change(function(){
                    check_a_f = $("#ckball_f").is(":checked");
                    if(check_a_f) {
                        $("#ckb_status_f").text("'. html_entity_decode(__d('two_authors', 'Tout décocher'), ENT_COMPAT | ENT_HTML401, 'utf-8') .'");
                    } else {
                        $("#ckb_status_f").text("' . __d('two_authors', 'Tout cocher') .'");
                    }
                    $(".ckbf").prop("checked", $(this).prop("checked"));
                });
                
                $("#ckball_m").change(function(){
                    check_a_m = $("#ckball_m").is(":checked");
                    if(check_a_m) {
                        $("#ckb_status_m").text("'. html_entity_decode(__d('two_authors', 'Tout décocher'), ENT_COMPAT | ENT_HTML401, 'utf-8') .'");
                    } else {
                        $("#ckb_status_m").text("' . __d('two_authors', 'Tout cocher') .'");
                    }
                    $(".ckbm").prop("checked", $(this).prop("checked"));
                });
            });
            //]]>
            </script>';
    }

}
