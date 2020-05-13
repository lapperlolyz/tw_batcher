<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/rebatcher/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/rebatcher')) {
            $cache->deleteTree(
                $dev . 'assets/components/rebatcher/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/rebatcher/', $dev . 'assets/components/rebatcher');
        }
        if (!is_link($dev . 'core/components/rebatcher')) {
            $cache->deleteTree(
                $dev . 'core/components/rebatcher/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/rebatcher/', $dev . 'core/components/rebatcher');
        }
    }
}

return true;