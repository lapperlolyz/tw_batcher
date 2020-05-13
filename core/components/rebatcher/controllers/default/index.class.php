<?php

class RebatcherIndexManagerController extends modExtraManagerController {
    public function process(array $scriptProperties = array()) {}
    public function getPageTitle() {
        return 'Rebatcher';
    }
    public function getTemplateFile() {
        return 'index.tpl';
    }

    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            let source = document.querySelector("#rebatcher-iframe")
            let target = document.querySelector("#modx-content .x-panel-body");
            target.appendChild(source);
        });
        </script>');
    }

}