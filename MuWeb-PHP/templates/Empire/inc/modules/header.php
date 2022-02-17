<?php
/**
 * 头部
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<div class="container menu" style="padding-right: 0;padding-left: 0">
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#"><img src="<?= __PATH_TEMPLATE_IMG__ ?>logo.png" width="130px" alt="<?= config('server_name') ?>"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand collapse navbar-collapse justify-content-end" id="navbarNav">
            <?=templateBuildNavbar()?>
        </div>
    </nav>
</div>

