<?php

class Nanga_Updates
{

    private $nanga;
    private $version;

    public function __construct($nanga, $version)
    {
        $this->author             = 'Panos Paganis';
        $this->name               = 'VG web things';
        $this->nanga              = $nanga;
        $this->remote_info_url    = 'https://api.github.com/repos/Mallinanga/nanga';
        $this->remote_version_url = 'https://api.github.com/repos/Mallinanga/nanga/releases';
        $this->version            = $version;
    }

    public function inject_update($transient)
    {
        $remote_version = $this->remote_version();
        if (version_compare($remote_version->tag_name, $this->version, '<=')) {
            return $transient;
        }
        $obj                                                             = new stdClass();
        $obj->slug                                                       = $this->nanga;
        $obj->plugin                                                     = $this->nanga . '/' . $this->nanga . '.php';
        $obj->new_version                                                = $remote_version->tag_name;
        $obj->url                                                        = $this->remote_info_url;
        $obj->package                                                    = $remote_version->zipball_url;
        $transient->response[$this->nanga . '/' . $this->nanga . '.php'] = $obj;

        return $transient;
    }

    private function remote_version()
    {
        if ( ! empty($_GET['force-check'])) {
            if (empty($_GET[$this->nanga . '-ignore-force-check'])) {
                delete_transient($this->nanga . '_cached_version');
            }
            $_GET[$this->nanga . '-ignore-force-check'] = true;
        }
        $cached_version = get_transient($this->nanga . '_cached_version');
        if ($cached_version) {
            return $cached_version;
        }
        $request = wp_remote_get($this->remote_version_url);
        if ( ! is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            $version = wp_remote_retrieve_body($request);
            $version = @json_decode($version);
            $version = $version[0];
            $timeout = 30 * MINUTE_IN_SECONDS;
        } else {
            $version = 0;
            $timeout = 5 * MINUTE_IN_SECONDS;
        }
        set_transient($this->nanga . '_cached_version', $version, $timeout);

        return $version;
    }

    public function inject_info($result, $action, $args)
    {
        if (isset($args->slug) && $args->slug == $this->nanga) {
            $remote_version = $this->remote_version();
            $info           = [
                'name'         => $this->name,
                'slug'         => $this->nanga,
                'version'      => $remote_version->tag_name,
                'author'       => $this->author,
                'last_updated' => $remote_version->published_at,
                'tested'       => get_bloginfo('version'),
                'sections'     => [
                    'changelog' => $remote_version->body,
                ],
            ];
            $obj            = new stdClass();
            foreach ($info as $k => $v) {
                $obj->$k = $v;
            }

            return $obj;
        }

        return $result;
    }

    public function post_install($true, $hook_extra, $result)
    {
        global $wp_filesystem;
        $proper_destination = WP_PLUGIN_DIR . '/' . $this->nanga;
        $wp_filesystem->move($result['destination'], $proper_destination);
        $result['destination'] = $proper_destination;

        return $result;
    }

    private function remote_info()
    {
        $request = wp_remote_get($this->remote_info_url);
        if ( ! is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            $info = wp_remote_retrieve_body($request);
            $info = @json_decode($info);
        } else {
            $info = false;
        }

        return $info;
    }
}
