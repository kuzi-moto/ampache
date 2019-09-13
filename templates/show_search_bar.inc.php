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
<?php
$search = T_('Search'); ?>
<div id="sb_Subsearch">
    <form name="search" method="post" action="<?php echo $web_path; ?>/search.php?type=song" enctype="multipart/form-data" style="Display:inline">
        <input type="text" name="rule_1_input" id="searchString" placeholder="<?php echo $search; ?>" />
        <input type="hidden" name="action" value="search" />
        <input type="hidden" name="rule_1_operator" value="0" />
        <input type="hidden" name="object_type" value="song" />
        <select name="rule_1" id="searchStringRule">
            <option value="anywhere"><?php echo T_('Anywhere')?></option>
            <option value="title"><?php echo T_('Titles')?></option>
            <option value="album"><?php echo $albums?></option>
            <option value="artist"><?php echo $artists?></option>
            <option value="playlist_name"><?php echo $playlists?></option>
            <option value="tag"><?php echo T_('Tags')?></option>
            <?php if (AmpConfig::get('label')) {
    ?>
                <option value="label"><?php echo T_('Labels')?></option>
            <?php
} ?>
            <?php if (AmpConfig::get('wanted')) {
        ?>
                <option value="missing_artist"><?php echo T_('Missing Artists')?></option>
            <?php
    } ?>
        </select>
        <?php if ($_SESSION['mobile']) {
        echo "<input class=\"button\" type=\"submit\" value=\"" . $search . "\"style=\"display: none;\" id=\"searchBtn\" />";
    } else {
        echo "<input class=\"button\" type=\"submit\" value=\"" . $search . "\" id=\"searchBtn\" />";
    }
?>
    </form>
</div>

