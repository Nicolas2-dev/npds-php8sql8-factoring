?>
<script type="textjavascript">
    //<![CDATA[
        document.addEventListener("DOMContentLoaded", function(e) {
            tinymce.init({
                selector: 'textarea.tin',
                
                mobile: {
                    menubar: true
                },

                language: '<?php $output['language']; ?>',

                <?php $output['tinyconf']; ?>
            });
        });
    //]]>
</script>
<?php