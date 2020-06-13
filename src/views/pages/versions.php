<?php $this->layout('_layout'); ?>

<div class="clearfix">
    <div class="float-left">
        <div class="float-left" style="margin-right: 8px">
            <?php $this->insert('partial::icon', ['icon' => $app['app_icon'] ?? 0]); ?>
        </div>
        <h2 class="float-left"><?=$app['name']?> - Versions</h2>
    </div>
    <div class="float-right">
        <a class="button" href="/view?app=<?=$appId?>">Edit config</a>
        <a class="button" href="/version_add?app=<?=$appId?>">Add version manually</a>
    </div>
</div>

<?php if ($success ?? false) : ?>
    <p id="alert" style="transition: all 300ms ease; color: greenyellow; background: green; padding: 6px">
        App config was updated successfully.
    </p>
<?php endif; ?>

<div class="clearfix">
    <div class="float-right">
        <input type="text" id="filter" name="filter" style="width: 300px" placeholder="Filter app by name ...">
    </div>
</div>
<table id="versions">
    <thead>
    <tr>
        <th>Version</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($versions as $key => $version) : ?>
        <tr>
            <td class="version-name">
                <?=$this->e($version) ?>
                <?php if ($version == $app['version']) : ?>
                (Current)
                <?php endif; ?>
            </td>
            <td style="width:300px; text-align: right;">
                <?php if ($version != $app['version']) : ?>
                    <form id="version-switch-<?=$key?>"
                          method="POST"
                          style="display: inline"
                          action="/version_switch?app=<?=$appId?>">
                        <input type="hidden" name="version" value="<?=$version?>">
                        <a href="#" onclick="document.getElementById('version-switch-<?=$key?>').submit()">[Switch to]</a>
                    </form>
                    -
                    <form id="version-remove-<?=$key?>"
                          method="POST"
                          style="display: inline"
                          action="/version_remove?app=<?=$appId?>">
                        <input type="hidden" name="version" value="<?=$version?>">
                        <a href="#" onclick="remove('version-remove-<?=$key?>');">[Remove]</a>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    function remove(formId) {
        const a = confirm("Are you sure that you want to remove this version?\n" +
            "Notice: Version folder will be removed to free up disk space.");
        if (a) {
            document.getElementById(formId).submit();
        }
    }
    $(function () {
        $('#filter').keyup(function () {
            const searchText = $(this).val();
            $('#versions tbody tr').each(function () {
                if ($(this).find('.version-name').text().toLowerCase().indexOf(searchText.toLowerCase()) === -1) {
                    $(this).css({'display': 'none'});
                } else {
                    $(this).css({'display': 'table-row'});
                }
            })
        });
    });
</script>
