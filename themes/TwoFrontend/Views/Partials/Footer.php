<footer id="footer" class="footer text-center mt-4">
    <div class="container">
        <p>!sc_infos!
            <br /><?= $msg_foot ?>
        </p>
        <script type="text/javascript">
            //<![CDATA[
            $(document).ready(function() {
                var translator = $('body').translate({
                    lang: "fr",
                    t: dict
                });
                translator.lang("fr");

                $('.plusdecontenu').click(function() {

                    var $this = $(this);
                    $this.toggleClass('plusdecontenu');

                    if ($this.hasClass('plusdecontenu')) {
                        $this.text(translator.get('Plus de contenu'));
                    } else {
                        $this.text(translator.get('Moins de contenu'));
                    }
                });

                if (matchMedia) {
                    const mq = window.matchMedia("(max-width: 991px)");
                    mq.addListener(WidthChange);
                    WidthChange(mq);
                }

                function WidthChange(mq) {
                    if (mq.matches) {
                        $("#col_LB, #col_RB").removeClass("show")
                    } else {
                        $("#col_LB, #col_RB").addClass("show")
                    }
                }
            });
            //]]
        </script>
    </div>
    <div class="container-fluid mt-4">
        <div class="row" style="background-color: #f5f5f5;">
            <div class="col-lg-5 text-start">
                <p class="text-muted pull-left mt-2">Copyright &copy; <?= date('Y'); ?> <a href="http://www.two.nicodev.fr/" target="_blank"><b>Two Cms <?= Config::get('two_core::version.Version_Num'); ?> / Two Framework <?= App::version(); ?></b></a></p>
            </div>
            <div class="col-lg-7" style="width:56%">
                <p class="text-muted pull-right mt-2" style="float: right!important;">
                    <small><!-- DO NOT DELETE! - Statistics --></small>
                </p>
            </div>
        </div>
    </div>
</footer>