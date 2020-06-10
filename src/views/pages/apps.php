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
            <td><?=$this->e($appId) ?></td>
            <td><?=$this->e($app['version']) ?></td>
            <td class="ali">
                <a href="/edit?app=<?=$appId ?>">[Edit]</a>
                <a href="/deregister?app=<?=$appId ?>">[Deregister]</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

