<?php $this->layout('_layout'); ?>

<div class="clearfix">
    <div class="float-left">
        <h2>Apps</h2>
    </div>
    <div class="float-right">
        <a class="button" href="/setup">Add / Setup new App</a>
    </div>
</div>
<table>
    <thead>
    <tr>
        <th>App name</th>
        <th>Version</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($apps as $appId => $app) : ?>
        <tr>
            <td><?=$this->e($app['name']) ?></td>
            <td style="width: 100px"><?=$this->e($app['version']) ?></td>
            <td class="ali" style="width:300px; text-align: right;">
                <a href="/versions?app=<?=$appId ?>">[Versions]</a> -
                <a href="/view?app=<?=$appId ?>">[Edit]</a> -
                <a href="/deregister?app=<?=$appId ?>">[Deregister]</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

