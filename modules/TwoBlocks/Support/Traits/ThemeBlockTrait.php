<?php

namespace Modules\TwoBlocks\Support\Traits;

use Modules\TwoBlocks\Support\Facades\Block;


trait ThemeBlockTrait 
{

    use PdstBlocksTrait;
    

    public function leftBlock($pdst)
    {
        $moreclass = 'col-12';

        switch ($pdst) {
            case '-1':
            case '3':
            case '5':
                echo '
                    </div>
                </div>
            </div>';
                break;
                
            case '1':
            case '2':
                echo '
                    </div>';
                $this->colsyst('#col_RB');
                echo '
                    <div id="col_RB" class="collapse show col-lg-3 ">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::rightblocks($moreclass);
                echo '
                        </div>
                    </div>
                </div>
            </div>';
                break;

            case '4':
                echo '
                </div>';
                $this->colsyst('#col_LB');
                echo '
                    <div id="col_LB" class="collapse show col-lg-3">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::leftblocks($moreclass);
                echo '
                    </div>
                </div>';
                $this->colsyst('#col_RB');
                echo '
                    <div id="col_RB" class="collapse show col-lg-3">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::rightblocks($moreclass);
                echo '
                        </div>
                    </div>
                </div>
            </div>';
                break;

            case '6':
                echo '
                </div>';
                $this->colsyst('#col_LB');
                echo '
                <div id="col_LB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::leftblocks($moreclass);
                echo '
                    </div>
                </div>
            </div>
            </div>';
                break;

            default:
                echo '
                    </div>
                </div>
            </div>';
                break;
        }
    }

    public function rightBlock($pdst)
    {
        $moreclass = 'col';

        echo '
            <div id="corps" class="container-fluid n-hyphenate">
                <div class="row g-3">';

        switch ($pdst) {
            case '-1':
                echo '
                    <div id="col_princ" class="col-12">';
                break;

            case '1':
                $this->colsyst('#col_LB');
                echo '
                    <div id="col_LB" class="collapse show col-lg-3">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::leftblocks($moreclass);
                echo '
                    </div>
                    </div>
                    <div id="col_princ" class="col-lg-6">';
                break;

            case '2':
            case '6':
                echo '
                <div id="col_princ" class="col-lg-9">';
                break;

            case '3':
                $this->colsyst('#col_LB');
                echo '
                <div id="col_LB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::leftblocks($moreclass);
                echo '
                    </div>
                </div>';
                $this->colsyst('#col_RB');
                echo ' 
                <div id="col_RB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::rightblocks($moreclass);
                echo '
                    </div>
                </div>
                <div id="col_princ" class="col-lg-6">';
                break;

            case '4':
                echo '
                    <div id="col_princ" class="col-lg-6">';
                break;
                
            case '5':
                $this->colsyst('#col_RB');
                echo '
                <div id="col_RB" class="collapse show col-lg-3">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::rightblocks($moreclass);
                echo '
                    </div>
                </div>
                <div id="col_princ" class="col-lg-9">';
                break;

            default:
                $this->colsyst('#col_LB');
                echo '
                    <div id="col_LB" class="collapse show col-lg-3">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';
                Block::leftblocks($moreclass);
                echo '
                        </div>
                    </div>
                    <div id="col_princ" class="col-lg-9">';
                break;
        }
    }

}