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
        $remoteVersion = $this->remoteVersion();
        if (version_compare($remoteVersion->tag_name, $this->version, '<=')) {
            return $transient;
        }
        $obj                                                             = new stdClass();
        $obj->slug                                                       = $this->nanga;
        $obj->plugin                                                     = $this->nanga . '/' . $this->nanga . '.php';
        $obj->new_version                                                = $remoteVersion->tag_name;
        $obj->url                                                        = $this->remote_info_url;
        $obj->package                                                    = $remoteVersion->zipball_url;
        $transient->response[$this->nanga . '/' . $this->nanga . '.php'] = $obj;

        return $transient;
    }

    private function remoteVersion()
    {
        if ( ! empty($_GET['force-check'])) {
            if (empty($_GET[$this->nanga . '-ignore-force-check'])) {
                delete_transient($this->nanga . '_cached_version');
            }
            $_GET[$this->nanga . '-ignore-force-check'] = true;
        }
        $cachedVersion = get_transient($this->nanga . '_cached_version');
        if ($cachedVersion) {
            return $cachedVersion;
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

    public function injectInfo($result, $action, $args)
    {
        if (isset($args->slug) && $args->slug == $this->nanga) {
            $remoteVersion = $this->remoteVersion();
            $info           = [
                'name'         => $this->name,
                'slug'         => $this->nanga,
                'version'      => $remoteVersion->tag_name,
                'author'       => $this->author,
                'last_updated' => $remoteVersion->published_at,
                'tested'       => get_bloginfo('version'),
                'sections'     => [
                    'changelog' => $remoteVersion->body,
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

    public function postInstall($true, $hook_extra, $result)
    {
        global $wp_filesystem;
        $properDestination = WP_PLUGIN_DIR . '/' . $this->nanga;
        $wp_filesystem->move($result['destination'], $properDestination);
        $result['destination'] = $properDestination;

        return $result;
    }

    private function remoteInfo()
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
