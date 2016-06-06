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
 * Action utils.
 *
 * @since 160531 Action utils.
 */
class Action extends Classes\SCore\Base\Core
{
    /**
     * Action var.
     *
     * @since 160531 Action utils.
     *
     * @type string Action var.
     */
    protected $var;

    /**
     * Data var.
     *
     * @since 160531 Action utils.
     *
     * @type string Data var.
     */
    protected $data_var;

    /**
     * Data slug.
     *
     * @since 160531 Action utils.
     *
     * @type string Data slug.
     */
    protected $data_slug;

    /**
     * Current action.
     *
     * @since 160531 Action utils.
     *
     * @type string Current action.
     */
    protected $action;

    /**
     * Registered actions.
     *
     * @since 160606 Action utils.
     *
     * @type array Registered actions.
     */
    protected $registered_actions;

    /**
     * Class constructor.
     *
     * @since 160531 Action utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $var_base                 = $this->App->Config->©brand['§action_base'];
        $this->var                = $this->App->Config->©brand[$var_base].'_action';
        $this->data_var           = $this->App->Config->©brand[$var_base].'_action_data';
        $this->data_slug          = $this->App->Config->©brand['©slug'].'-action-data';
        $this->action             = $_REQUEST[$this->var] ?? '';
        $this->registered_actions = []; // Initialize.

        $this->register('§save-options', '§Options', 'onActionSaveOptions');
        $this->register('§save-options...via-ajax', '§Options', 'onActionSaveOptionsViaAjax');
        $this->register('§restore-default-options', '§Options', 'onActionRestoreDefaultOptions');
        $this->register('§dismiss-notice', '§Notices', 'onActionDismissNotice');
    }

    /**
     * Handle actions.
     *
     * @since 160531 Action utils.
     */
    public function onWpLoaded()
    {
        if (!$this->action) {
            return; // N/A.
        }
        $this->c::noCacheFlags();
        $this->c::noCacheHeaders();

        if (preg_match('/\.{3}via\-ajax$/u', $this->action)) {
            header('content-type: application/json; charset=utf-8');
            $this->c::isAjax(true);
        } elseif (preg_match('/\.{3}via\-api$/u', $this->action)) {
            header('content-type: application/json; charset=utf-8');
            $this->c::isApi(true);
        }
        $this->handle(); // See below.
    }

    /**
     * Handle actions.
     *
     * @since 160531 Action utils.
     *
     * @note Only runs when appropriate.
     */
    protected function handle()
    {
        if (!isset($this->registered_actions[$this->action])) {
            $this->s::dieInvalid(); // Unregistered!
        }
        $actor = $this->registered_actions[$this->action];

        if ($actor['requires_valid_nonce']) {
            $this->s::requireValidNonce($this->action);
        }
        $this->c::doingAction($this->action);

        $Utility = $this->App->Utils->{$actor['class']};
        $Utility->{$actor['method']}($this->action);
    }

    /**
     * Action data.
     *
     * @since 160531 Action utils.
     *
     * @return mixed Action data.
     */
    public function data()
    {
        if (!$this->action) {
            return; // Not applicable.
        } elseif (!$this->c::doingAction()) {
            return; // Not appliable.
        }
        if (($data = $_REQUEST[$this->data_var] ?? null)) {
            $data = $this->c::mbTrim($this->c::unslash($data));
        }
        return $data; // Trimmed and stripped data (possible `null` value).
    }

    /**
     * Best URL for the action.
     *
     * @since 160531 Action utils.
     *
     * @param string $action Action identifier.
     *
     * @return string Best URL for the action.
     */
    public function bestUrl(string $action): string
    {
        if (preg_match('/\.{3}via\-(?:ajax|api)$/u', $action)) {
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
     * @since 160531 Action utils.
     *
     * @param string      $action Action identifier.
     * @param string|null $url    Input URL (optional).
     * @param mixed|null  $data   Action data (optional).
     *
     * @return string URL w/ an action.
     */
    public function urlAdd(string $action, string $url = null, $data = null): string
    {
        $url        = $url ?? $this->bestUrl($action);
        $url        = $this->c::addUrlQueryArgs([$this->var => $action], $url);
        $url        = isset($data) ? $this->c::addUrlQueryArgs([$this->data_var => $data], $url) : $url;
        return $url = $this->s::addUrlNonce($url, $action);
    }

    /**
     * Remove an nonce from a URL.
     *
     * @since 160524 First documented version.
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
     * @since 160531 Action utils.
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
     * @since 160531 Action utils.
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
     * @since 160531 Action utils.
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
     * @since 160531 Action utils.
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
        $default_args = [
            'requires_valid_nonce' => !preg_match('/\.{3}via\-api$/u', $action),
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $this->registered_actions[$action] = array_merge($args, compact('class', 'method'));
    }
}
