<?php $this->layout('_layout'); ?>

<div class="clearfix">
    <div class="float-left">
        <h2>Apps</h2>
    </div>
    <div class="float-right">
        <a class="button" href="/setup">Add / Setup new App</a>
    </div>
</div>
<div class="clearfix">
    <div class="float-right">
        <input type="text" id="filter" name="filter" style="width: 300px" placeholder="Filter app by name ...">
    </div>
</div>
<table id="apps" style="table-layout: fixed">
    <thead>
    <tr>
        <th style="width: 48px"></th>
        <th>App name</th>
        <th>Version</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php $a = 0; ?>
    <?php foreach ($apps as $appId => $app) : ?>
        <tr>
            <td style="padding-right:0;">
                <?php $this->insert('partial::icon', ['icon' => $app['app_icon'] ?? 0]); ?>
            </td>
            <td class="app-name"><?=$this->e($app['name']) ?></td>
            <td style="width: 100px"><?=$this->e($app['version']) ?></td>
            <td class="ali" style="width:300px; text-align: right;">
                <a href="/versions?app=<?=$appId ?>">[Versions]</a> -
                <a href="/view?app=<?=$appId ?>">[Edit]</a> -
                <form id="app_<?=$appId?>"
                      method="POST"
                      style="display: inline"
                      action="/deregister?app=<?=$appId?>">
                    <a href="#" onclick="deregister('app_<?=$appId?>');">[Deregister]</a>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    function deregister(formId) {
        const a = confirm("Are you sure that you want to deregister this app configuration?\n" +
            "Notice: Directory itself will not be deleted.");
        if (a) {
            document.getElementById(formId).submit();
        }
    }
    $(function () {
        $('#filter').keyup(function() {
            const searchText = $(this).val();
            $('#apps tbody tr').each(function() {
                if ($(this).find('.app-name').text().toLowerCase().indexOf(searchText.toLowerCase()) === -1) {
                    $(this).css({'display': 'none'});
                } else {
                    $(this).css({'display': 'table-row'});
                }
            })
        });
    });
</script>
