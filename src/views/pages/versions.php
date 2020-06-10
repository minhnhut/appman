<?php $this->layout('_layout'); ?>

<div class="clearfix">
    <div class="float-left">
        <h2><?=$app['name']?> - Versions</h2>
    </div>
    <div class="float-right">
        <a class="button" href="/version_add?app=<?=$appId?>">Add new version manually</a>
    </div>
</div>
<table>
    <thead>
    <tr>
        <th>Version</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($versions as $version) : ?>
        <tr>
            <td>
                <?=$this->e($version) ?>
                <?php if ($version == $app['version']) : ?>
                (Current)
                <?php endif; ?>
            </td>
            <td style="width:300px; text-align: right;">
                <?php if ($version != $app['version']) : ?>
                    <form id="version<?=$version?>"
                          method="POST"
                          style="display: inline"
                          action="/version_switch?app=<?=$appId?>">
                        <input type="hidden" name="version" value="<?=$version?>">
                        <a href="#" onclick="document.getElementById('version<?=$version?>').submit()">[Switch to]</a>
                    </form>
                    -
                    <a href="/version_remove?app=<?=$appId ?>&version=<?=$version ?>">[Remove]</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

