<?php

class RebatcherIndexManagerController extends modExtraManagerController {
    public function process(array $scriptProperties = array()) {}
    public function getPageTitle() {
        return 'Rebatcher';
    }
    public function getTemplateFile() {
        return 'index.tpl';
    }
}