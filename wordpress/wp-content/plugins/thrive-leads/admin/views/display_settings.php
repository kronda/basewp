<script type="application/javascript">
    var ThriveLeads = ThriveLeads || {};

    (function () {
        'use strict';

        ThriveLeads.objects.hangers = new ThriveLeads.collections.Hangers(<?php echo json_encode($hangers)?>);
        ThriveLeads.objects.savedTemplates = new ThriveLeads.collections.Templates(<?php echo json_encode($savedTemplates)?>);

        var displayGroupSettings = new ThriveLeads.views.DisplayGroupSettings({
            collection: ThriveLeads.objects.hangers
        });

        displayGroupSettings.urlSaveOptions = '<?php echo 'admin-ajax.php?action=thrive_leads_backend_ajax&route=saveGroupSettings' ?>';
        displayGroupSettings.urlSaveTemplate = '<?php echo 'admin-ajax.php?action=thrive_leads_backend_ajax&route=saveGroupTemplate' ?>';
        displayGroupSettings.group = '<?php echo $_GET['group'] ?>';

        displayGroupSettings.render();

    })(jQuery);
</script>