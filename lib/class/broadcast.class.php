<?php
/* vim:set softtabstop=4 shiftwidth=4 expandtab: */
/**
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright 2001 - 2014 Ampache.org
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

class Broadcast extends database_object implements library_item
{
    public $id;
    public $started;
    public $listeners;
    public $song;
    public $song_position;
    public $name;
    public $user;

    public $tags;
    public $f_name;
    public $f_link;
    public $f_tags;

    /**
     * Constructor
     */
    public function __construct($id=0)
    {
        if (!$id) { return true; }

        /* Get the information from the db */
        $info = $this->get_info($id);

        // Foreach what we've got
        foreach ($info as $key=>$value) {
            $this->$key = $value;
        }

        return true;
    } //constructor

    public function update_state($started, $key='')
    {
        $sql = "UPDATE `broadcast` SET `started` = ?, `key` = ?, `song` = '0', `listeners` = '0' WHERE `id` = ?";
        Dba::write($sql, array($started, $key, $this->id));

        $this->started = $started;
    }

    public function update_listeners($listeners)
    {
        $sql = "UPDATE `broadcast` SET `listeners` = ? " .
            "WHERE `id` = ?";
        Dba::write($sql, array($listeners, $this->id));
        $this->listeners = $listeners;
    }

    public function update_song($song_id)
    {
        $sql = "UPDATE `broadcast` SET `song` = ? " .
            "WHERE `id` = ?";
        Dba::write($sql, array($song_id, $this->id));
        $this->song = $song_id;
        $this->song_position = 0;
    }

    public function delete()
    {
        $sql = "DELETE FROM `broadcast` WHERE `id` = ?";
        return Dba::write($sql, array($this->id));
    }

    public static function create($name, $description='')
    {
        if (!empty($name)) {
            $sql = "INSERT INTO `broadcast` (`user`, `name`, `description`, `is_private`) VALUES (?, ?, ?, '1')";
            $params = array($GLOBALS['user']->id, $name, $description);
            Dba::write($sql, $params);
            return Dba::insert_id();
        }

        return 0;
    }

    public function update($data)
    {
        if (isset($data['edit_tags'])) {
            Tag::update_tag_list($data['edit_tags'], 'broadcast', $this->id);
        }

        $sql = "UPDATE `broadcast` SET `name` = ?, `description` = ?, `is_private` = ? " .
            "WHERE `id` = ?";
        $params = array($data['name'], $data['description'], !empty($data['private']), $this->id);
        Dba::write($sql, $params);

        return $this->id;
    }

    public function format()
    {
        $this->f_name = $this->name;
        $this->f_link = '<a href="' . AmpConfig::get('web_path') . '/broadcast.php?id=' . $this->id . '">' . scrub_out($this->f_name) . '</a>';
        $this->tags = Tag::get_top_tags('broadcast', $this->id);
        $this->f_tags = Tag::get_display($this->tags, true, 'broadcast');
    }

    public function get_keywords()
    {
        return array();
    }

    public function get_fullname()
    {
        return $this->f_name;
    }

    public function get_parent()
    {
        return null;
    }

    public function get_childrens()
    {
        return array();
    }

    public function get_medias($filter_type = null)
    {
        // Not a media, shouldn't be that
        $medias = array();
        if (!$filter_type || $filter_type == 'broadcast') {
            $medias[] = array(
                'object_type' => 'broadcast',
                'object_id' => $this->id
            );
        }
        return $medias;
    }

    public function get_user_owner()
    {
        return $this->user;
    }

    public function get_default_art_kind()
    {
        return 'default';
    }

    public static function get_broadcast_list_sql()
    {
        $sql = "SELECT `id` FROM `broadcast` WHERE `started` = '1' ";

        return $sql;
    }

    public static function get_broadcast_list()
    {
        $sql = self::get_broadcast_list_sql();
        $db_results = Dba::read($sql);
        $results = array();

        while ($row = Dba::fetch_assoc($db_results)) {
            $results[] = $row['id'];
        }

        return $results;
    }

    public static function generate_key()
    {
        // Should be improved for security reasons!
        return md5(uniqid(rand(), true));
    }

    public static function get_broadcast($key)
    {
        $sql = "SELECT `id` FROM `broadcast` WHERE `key` = ?";
        $db_results = Dba::read($sql, array($key));

        if ($results = Dba::fetch_assoc($db_results)) {
            return new Broadcast($results['id']);
        }

        return null;
    }

    public function show_action_buttons()
    {
        if ($this->id) {
            if ($GLOBALS['user']->has_access('75')) {
                echo "<a id=\"edit_broadcast_ " . $this->id . "\" onclick=\"showEditDialog('broadcast_row', '" . $this->id . "', 'edit_broadcast_" . $this->id . "', '" . T_('Broadcast edit') . "', 'broadcast_row_')\">" . UI::get_icon('edit', T_('Edit')) . "</a>";
                echo " <a href=\"" . AmpConfig::get('web_path') . "/broadcast.php?action=show_delete&id=" . $this->id ."\">" . UI::get_icon('delete', T_('Delete')) . "</a>";
            }
        }
    }

    public static function get_broadcast_link()
    {
        $link = "<div class=\"broadcast-action\">";
        $link .= "<a href=\"#\" onclick=\"showBroadcastsDialog(event);\">" . UI::get_icon('broadcast', T_('Broadcast')) . "</a>";
        $link .= "</div>";
        return $link;
    }

    public static function get_unbroadcast_link($id)
    {
        $link = "<div class=\"broadcast-action\">";
        $link .= Ajax::button('?page=player&action=unbroadcast&broadcast_id=' . $id, 'broadcast', T_('Unbroadcast'), 'broadcast_action');
        $link .= "</div>";
        $link .= "<div class=\"broadcast-info\">(<span id=\"broadcast_listeners\">0</span>)</div>";
        return $link;
    }

    public static function get_broadcasts($user_id)
    {
        $sql = "SELECT `id` FROM `broadcast` WHERE `user` = ?";
        $db_results = Dba::read($sql, array($user_id));

        $broadcasts = array();
        while ($results = Dba::fetch_assoc($db_results)) {
            $broadcasts[] = $results['id'];
        }
        return $broadcasts;
    }

    public static function gc()
    {

    }

    /*
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function play_url($oid, $additional_params='')
    {
        return $oid;
    }

} // end of broadcast class
