<?php
/* vim:set softtabstop=4 shiftwidth=4 expandtab: */
/**
 *
 * LICENSE: GNU Affero General Public License, version 3 (AGPLv3)
 * Copyright 2001 - 2019 Ampache.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
?>
<?php UI::show_box_top(T_('Importing a Playlist from a File'), 'box box_import_playlist'); ?>
<form method="post" name="import_playlist" action="<?php echo AmpConfig::get('web_path'); ?>/playlist.php" enctype="multipart/form-data">
    <table class="tabledata">
        <tr>
            <td>
                <?php echo T_('Filename'); ?> (<?php echo AmpConfig::get('catalog_playlist_pattern'); ?>):
            </td>
            <td><input type="file" name="filename" value="<?php echo scrub_out($_REQUEST['filename']); ?>" /></td>
        </tr>
    </table>
    <div class="formValidation">
        <input type="hidden" name="action" value="import_playlist" />
        <input type="submit" value="<?php echo T_('Import Playlist'); ?>" />
    </div>
</form>
<?php UI::show_box_bottom(); ?>
