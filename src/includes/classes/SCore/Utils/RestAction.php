<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * ReST action utils.
 *
 * @since 160608 Action utils.
 */
class RestAction extends Classes\SCore\Base\Core
{
    /**
     * Action var.
     *
     * @since 160608 ReST utils.
     *
     * @type string Action var.
     */
    protected $var;

    /**
     * Data var.
     *
     * @since 160608 ReST utils.
     *
     * @type string Data var.
     */
    protected $data_var;

    /**
     * Data slug.
     *
     * @since 160608 ReST utils.
     *
     * @type string Data slug.
     */
    protected $data_slug;

    /**
     * Current action.
     *
     * @since 160608 ReST utils.
     *
     * @type string Current action.
     */
    protected $action;

    /**
     * Action API version.
     *
     * @since 160608 ReST utils.
     *
     * @type string Action API version.
     */
    protected $api_version;

    /**
     * Registered actions.
     *
     * @since 160608 ReST utils.
     *
     * @type array Registered actions.
     */
    protected $registered_actions;

    /**
     * Class constructor.
     *
     * @since 160608 ReST utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->var       = $this->App->Config->©brand['©short_var'].'_action';
        $this->data_var  = $this->App->Config->©brand['©short_var'].'_data';
        $this->data_slug = $this->App->Config->©brand['©slug'].'-action-data';

        $this->action             = $this->api_version             = '';
        $this->registered_actions = []; // Initialize registered actions.

        $this->register('§save-options', '§Options', 'onRestActionSaveOptions');
        $this->register('ajax.§save-options', '§Options', 'onAjaxRestActionSaveOptions');
        $this->register('§restore-default-options', '§Options', 'onRestActionRestoreDefaultOptions');
        $this->register('§dismiss-notice', '§Notices', 'onRestActionDismissNotice');
    }

    /**
     * Handle actions.
     *
     * @since 160608 ReST utils.
     */
    public function onWpLoaded()
    {
        if (empty($_REQUEST[$this->var])) {
            return; // Not applicable.
        }
        $this->action = (string) $_REQUEST[$this->var];
        $this->action = $this->c::unslash($this->action);
        $this->action = $this->c::mbTrim($this->action);

        $this->c::noCacheFlags(); // Flag as do NOT cache.
        $this->c::noCacheHeaders(); // Send headers also.

        if ($this->viaAjax($this->action)) {
            header('content-type: application/json; charset=utf-8');
            $this->c::isAjax(true);
        } elseif ($this->viaApi($this->action)) {
            header('content-type: application/json; charset=utf-8');
            $this->api_version = $this->parseApiVersion($this->action);
            $this->action      = $this->stripApiVersion($this->action);
            $this->c::isApi(true);
        }
        $this->c::doingRestAction($this->action);

        if (!isset($this->registered_actions[$this->action])) {
            $this->s::dieInvalid(); // Unregistered!
        }
        $actor = $this->registered_actions[$this->action];

        if ($actor['requires_valid_nonce']) {
            $this->s::requireValidNonce($this->action);
        }
        $Utility = $this->App->Utils->{$actor['class']};
        $Utility->{$actor['method']}($this->action);
    }

    /**
     * Action data.
     *
     * @since 160608 ReST utils.
     *
     * @return mixed Action data.
     */
    public function data()
    {
        if (!$this->action) {
            return; // Not applicable.
        }
        if (($data = $_REQUEST[$this->data_var] ?? null)) {
            $data = $this->c::mbTrim($this->c::unslash($data));
        }
        return $data; // Trimmed and stripped data (possible `null` value).
    }

    /**
     * Action API version.
     *
     * @since 160625 ReST utils.
     *
     * @return mixed Action data.
     */
    public function apiVersion()
    {
        if (!$this->action) {
            return ''; // Not applicable.
        }
        return $this->api_version; // For API actions.
    }

    /**
     * Best URL for the action.
     *
     * @since 160608 ReST utils.
     *
     * @param string $action Action identifier.
     *
     * @return string Best URL for the action.
     */
    public function bestUrl(string $action): string
    {
        if (preg_match('/^(?:ajax\.|api(?:\.|\-v(?:[0-9]\.)+))/u', $action)) {
            return home_url('/'); // Both ride on index.
        }
        $is_admin = is_admin(); // Need this for the checks below.

        if ($is_admin && ($this->s::isOwnMenuPage() || $this->s::isOwnMenuPageTab())) {
            return $this->urlRemove($this->c::currentUrl());
        } elseif ($is_admin && !$this->c::isAjax()) {
            return self_admin_url('/');
        }
        return home_url('/'); // Fallback (default).
    }

    /**
     * Add action to a URL.
     *
     * @since 160608 ReST utils.
     *
     * @param string      $action Action identifier.
     * @param string|null $url    Input URL (optional).
     * @param mixed|null  $data   Action data (optional).
     *
     * @return string URL w/ an action.
     */
    public function urlAdd(string $action, string $url = null, $data = null): string
    {
        if (preg_match('/^api\./u', $action)) {
            $action = preg_replace('/^api\./u', '', $action);
            $action = 'api-v'.$this->App::REST_ACTION_API_VERSION.'.'.$action;
        } // This forces a version into URLs that call upon an API action.

        $url        = $url ?? $this->bestUrl($action);
        $url        = $this->c::addUrlQueryArgs([$this->var => $action], $url);
        $url        = isset($data) ? $this->c::addUrlQueryArgs([$this->data_var => $data], $url) : $url;
        return $url = $this->s::addUrlNonce($url, $action);
    }

    /**
     * Remove an nonce from a URL.
     *
     * @since 160608 ReST utils.
     *
     * @param string $url Input URL.
     *
     * @return string URL w/o an nonce.
     */
    public function urlRemove(string $url): string
    {
        $url        = $this->c::removeUrlQueryArgs([$this->var, $this->data_var], $url);
        return $url = $this->s::removeUrlNonce($url);
    }

    /**
     * Data form element ID.
     *
     * @since 160608 ReST utils.
     *
     * @param string $action Action identifier.
     * @param string $var    Data var (array key).
     *
     * @return string Data form element ID.
     */
    public function formElementId(string $action, string $var): string
    {
        return $this->data_slug.'-'.$this->c::varToSlug($action).'-'.$this->c::varToSlug($var);
    }

    /**
     * Data form element class.
     *
     * @since 160608 ReST utils.
     *
     * @param string $var Data var (array key).
     *
     * @return string Data form element class.
     */
    public function formElementClass(string $var): string
    {
        return $this->data_slug.'-'.$this->c::varToSlug($var);
    }

    /**
     * Data form element name.
     *
     * @since 160608 ReST utils.
     *
     * @param string $var Data var (array key).
     *
     * @return string Data form element name.
     */
    public function formElementName(string $var): string
    {
        return $this->data_var.'['.$var.']';
    }

    /**
     * Registers an action.
     *
     * @since 160608 ReST utils.
     *
     * @param string $action Action identifier.
     * @param string $class  A utility class name.
     * @param string $method A utility class method callback.
     * @param array  $args   Any additional behavioral args.
     */
    public function register(string $action, string $class, string $method, array $args = [])
    {
        if (!$action || !$class || !$method) {
            throw $this->c::issue('Action args empty.');
        }
        if (($via_api = $this->viaApi($action))) {
            $action = $this->stripApiVersion($action);
        }
        $default_args = [ // API = no nonce (default).
            'requires_valid_nonce' => $via_api ? false : true,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $this->registered_actions[$action] = array_merge($args, compact('class', 'method'));
    }

    /**
     * An action via AJAX?
     *
     * @since 160625 ReST utils.
     *
     * @param string $action Action identifier.
     *
     * @return bool True if it's an action via AJAX.
     */
    protected function viaAjax(string $action): bool
    {
        return mb_strpos($action, 'ajax.') === 0;
    }

    /**
     * An action via API?
     *
     * @since 160625 ReST utils.
     *
     * @param string $action Action identifier.
     *
     * @return bool True if it's an action via API.
     */
    protected function viaApi(string $action): bool
    {
        if (mb_strpos($action, 'api.') === 0) {
            return true; // Saves time in many cases.
        }
        return mb_strpos($action, 'api-v') === 0 && preg_match('/^api\-v[0-9]+[0-9.]*\./u', $action);
    }

    /**
     * Parse API version.
     *
     * @since 160625 ReST utils.
     *
     * @param string $action Action identifier.
     *
     * @return string API version from action identifier.
     */
    protected function parseApiVersion(string $action): string
    {
        if (mb_strpos($action, 'api-v') === 0) {
            $version = preg_replace('/^api\-v([0-9]+[0-9.]*)\..+$/u', '${1}', $action);
        }
        return $version ?? ''; // API version from action identifier.
    }

    /**
     * Strip API version.
     *
     * @since 160625 ReST utils.
     *
     * @param string $action Action identifier.
     *
     * @return string Action w/o the API version.
     */
    protected function stripApiVersion(string $action): string
    {
        if (mb_strpos($action, 'api-v') === 0) {
            $action = preg_replace('/^api\-v[0-9]+[0-9.]*\./u', 'api.', $action);
        }
        return $action; // Without an API version.
    }
}
